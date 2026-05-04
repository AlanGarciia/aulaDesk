<?php

namespace App\Http\Controllers;

use App\Models\Notificacio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificacioController extends Controller
{
    private const POLL_LIMIT = 15;
    private const PAGE_SIZE = 30;

    private function espaiId(Request $request): int
    {
        $id = (int) $request->session()->get('espai_id');
        abort_unless($id, 403);
        return $id;
    }

    private function usuariEspaiId(Request $request): int
    {
        $id = (int) $request->session()->get('usuari_espai_id');
        abort_unless($id, 403);
        return $id;
    }

    public function poll(Request $request): JsonResponse
    {
        $espaiId = $this->espaiId($request);
        $usuariEspaiId = $this->usuariEspaiId($request);

        $unreadCount = Notificacio::query()
            ->where('espai_id', $espaiId)
            ->where('usuari_espai_id', $usuariEspaiId)
            ->whereNull('llegida_el')
            ->count();

        $items = Notificacio::query()
            ->where('espai_id', $espaiId)
            ->where('usuari_espai_id', $usuariEspaiId)
            ->orderByDesc('created_at')
            ->limit(self::POLL_LIMIT)
            ->get(['id','tipus','titol','missatge','url','llegida_el','created_at'])
            ->map(function (Notificacio $n) {
                return [
                    'id' => (int) $n->id,
                    'tipus' => (string) $n->tipus,
                    'titol' => (string) $n->titol,
                    'missatge' => $n->missatge,
                    'url' => $n->url,
                    'llegida' => $n->llegida_el !== null,
                    'creat' => optional($n->created_at)->diffForHumans(),
                    'icona' => $this->iconaPerTipus((string) $n->tipus),
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'items' => $items,
        ]);
    }

    public function markRead(Request $request, int $id): JsonResponse
    {
        $espaiId = $this->espaiId($request);
        $usuariEspaiId = $this->usuariEspaiId($request);

        $updated = Notificacio::query()
            ->where('id', $id)
            ->where('espai_id', $espaiId)
            ->where('usuari_espai_id', $usuariEspaiId)
            ->whereNull('llegida_el')
            ->update(['llegida_el' => now()]);

        return response()->json(['ok' => true, 'updated' => $updated]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $espaiId = $this->espaiId($request);
        $usuariEspaiId = $this->usuariEspaiId($request);

        $updated = Notificacio::query()
            ->where('espai_id', $espaiId)
            ->where('usuari_espai_id', $usuariEspaiId)
            ->whereNull('llegida_el')
            ->update(['llegida_el' => now()]);

        return response()->json(['ok' => true, 'updated' => $updated]);
    }

    public function index(Request $request)
    {
        $espaiId = $this->espaiId($request);
        $usuariEspaiId = $this->usuariEspaiId($request);

        $filtre = (string) $request->query('filtre', 'totes');
        $tipus = (string) $request->query('tipus', '');

        $q = Notificacio::query()
            ->where('espai_id', $espaiId)
            ->where('usuari_espai_id', $usuariEspaiId)
            ->orderByDesc('created_at');

        if ($filtre === 'no_llegides') {
            $q->whereNull('llegida_el');
        } elseif ($filtre === 'llegides') {
            $q->whereNotNull('llegida_el');
        }

        if ($tipus !== '' && in_array($tipus, [
            'noticia_creada','usuari_nou','guardia_solicitada','guardia_acceptada',
        ], true)) {
            $q->where('tipus', $tipus);
        }

        $notificacions = $q->paginate(self::PAGE_SIZE)->withQueryString();

        return view('espai.notificacions.index', [
            'notificacions' => $notificacions,
            'filtre' => $filtre,
            'tipusSeleccionat' => $tipus,
        ]);
    }

    private function iconaPerTipus(string $tipus): string
    {
        return match ($tipus) {
            'noticia_creada' => 'bi-journal-text',
            'usuari_nou' => 'bi-person-plus',
            'guardia_solicitada' => 'bi-clock-history',
            'guardia_acceptada' => 'bi-check2-circle',
            default => 'bi-bell',
        };
    }
}