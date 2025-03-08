<?php
// includes/functions.php

function getActivityIcon($type) {
    return match($type) {
        'tour'           => 'fa-map-marked-alt',
        'adventure'      => 'fa-mountain',
        'cultural'       => 'fa-landmark',
        'dining'         => 'fa-utensils',
        'entertainment'  => 'fa-ticket-alt',
        'transportation' => 'fa-car',
        'meeting'        => 'fa-handshake',
        'concert'        => 'fa-music',
        'flights'        => 'fa-plane',
        'car_rental'     => 'fa-car-side',
        'restaurant'     => 'fa-utensils',
        default          => 'fa-calendar-day'
    };
}
