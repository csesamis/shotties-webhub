<?php
// Single source for the activity list, shared by activities.php and activity_reg.php.

/** Sample activities in Kuching. Dates are relative so they always stay in the future. */
function get_activities(): array
{
    return [
        [
            'id'    => 'photocard-swap-day',
            'title' => 'Photocard Swap Day',
            'type'  => 'Trading meetup',
            'when'  => new DateTime('next saturday 14:00'),
            'hours' => 3,
            'price' => 10.00,
            'venue' => 'Community Hall, Padungan, Kuching',
            'about' => 'Bring your spares and trade face to face with other Shotties. Sleeves and a trading table are provided.',
        ],
        [
            'id'    => 'album-listening-party',
            'title' => 'Album Listening Party',
            'type'  => 'Listening party',
            'when'  => new DateTime('next saturday +1 week 19:30'),
            'hours' => 2,
            'price' => 15.00,
            'venue' => 'Function Room, Jalan Song, Kuching',
            'about' => 'Listen to the latest release together, then vote on favourite tracks over drinks.',
        ],
        [
            'id'    => 'binder-making-workshop',
            'title' => 'Photocard Binder Workshop',
            'type'  => 'Workshop',
            'when'  => new DateTime('next sunday +1 week 10:00'),
            'hours' => 2,
            'price' => 35.00,
            'venue' => 'Craft Studio, Jalan Tabuan, Kuching',
            'about' => 'Make your own decorated A5 binder. All materials included, take it home the same day.',
        ],
    ];
}