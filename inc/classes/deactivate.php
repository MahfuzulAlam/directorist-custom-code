<?php

class Deactivate_Directorist_Statistics
{
    public function __construct()
    {
        flush_rewrite_rules();
    }
}

new Deactivate_Directorist_Statistics();