<?php
// Basic
Router::promote(
        count(
                CroogoRouter::connect('/', array(
                    'plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index', 'type' => 'video'
                    )
                )
        ) -1
);

CroogoRouter::contentType('video');
