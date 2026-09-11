@props(['status'])

@php
    $statuses = [
        'waiting_payment' => ['label' => 'Menunggu Pembayaran', 'class' => 'badge-warning'],
        'processed' => ['label' => 'Diproses', 'class' => 'badge-primary'],
        'packing' => ['label' => 'Dikemas', 'class' => 'badge-primary'],
        'shipped' => ['label' => 'Dikirim', 'class' => 'badge-primary'],
        'delivered' => ['label' => 'Selesai', 'class' => 'badge-success'],
        'cancelled' => ['label' => 'Dibatalkan', 'class' => 'badge-error'],
    ];
    $entry = $statuses[$status] ?? ['label' => ucwords(str_replace('_', ' ', $status)), 'class' => 'badge-muted'];
@endphp

<span class="badge {{ $entry['class'] }}">{{ $entry['label'] }}</span>