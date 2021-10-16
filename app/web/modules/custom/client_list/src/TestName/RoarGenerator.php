<?php

namespace Drupal\client_list\TestName;

class RoarGenerator extends ControllerBase {
    public function getRoar(length) {
        return 'R'.str_repeat('O', $length).'AR';

    }
}