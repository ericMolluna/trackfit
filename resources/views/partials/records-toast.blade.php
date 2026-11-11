{{--
    Aviso de récords batidos al terminar un entreno.
    Se incluye en rutina-seguir.blade.php con
    @include('partials.records-toast')
--}}
@php
    $newRecords = session('records', []);
@endphp

@if (!empty($newRecords))
    <style>
        .tf-records-toast {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto 22px;
            padding: 18px 20px;
            border: 1px solid rgba(250, 204, 21, .45);
            border-radius: 18px;
            background:
                linear-gradient(135deg, rgba(250, 204, 21, .10), transparent 60%),
                #0b0b0d;
            color: #fff;
            text-align: left;
            box-sizing: border-box;
        }

        .tf-records-toast-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
        }

        .tf-records-toast-title {
            margin: 0;
            color: #fde68a;
            font-size: 1.05rem;
            font-weight: 800;
        }

        .tf-records-toast-close {
            border: 0;
            background: transparent;
            color: rgba(255, 255, 255, .7);
            font-size: 1.4rem;
            line-height: 1;
            cursor: pointer;
        }

        .tf-records-toast ul {
            display: grid;
            gap: 6px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .tf-records-toast li {
            color: rgba(255, 255, 255, .85);
            font-size: .88rem;
            line-height: 1.5;
        }

        .tf-records-toast a {
            display: inline-block;
            margin-top: 12px;
            color: #fde68a;
            font-size: .82rem;
        }
    </style>

    <section class="tf-records-toast" role="status" aria-live="polite" data-records-toast>
        <div class="tf-records-toast-head">
            <h2 class="tf-records-toast-title">
                🏆 {{ count($newRecords) === 1 ? '¡Nuevo récord personal!' : '¡' . count($newRecords) . ' nuevos récords personales!' }}
            </h2>

            <button
                type="button"
                class="tf-records-toast-close"
                aria-label="Cerrar aviso de récords"
                onclick="this.closest('[data-records-toast]')?.remove()"
            >
                ×
            </button>
        </div>

        <ul>
            @foreach ($newRecords as $record)
                <li>
                    <strong>{{ $record['exercise'] }}</strong> ·
                    {{ $record['type'] }}:
                    {{ \App\Support\PersonalRecords::format($record['value']) }} kg

                    @if ($record['previous'] !== null)
                        (antes {{ \App\Support\PersonalRecords::format($record['previous']) }} kg)
                    @endif
                </li>
            @endforeach
        </ul>

        <a href="{{ route('progress.records') }}">Ver todos mis récords →</a>
    </section>
@endif
