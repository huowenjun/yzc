<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class Modal extends AbstractDisplayer
{
    public function display($placement = 'left')
    {
        Admin::script("$('[data-toggle=\"modal\"]').modal()");

        return <<<EOT
<button type="button"
    class="btn btn-secondary"
    title="Modal"
    data-container="body"
    data-toggle="modal"
    data-placement="$placement"
    data-content="{$this->value}"
    >
  点击查看
</button>

EOT;

    }
}