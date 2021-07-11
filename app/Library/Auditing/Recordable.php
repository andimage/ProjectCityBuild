<?php

namespace App\Library\Auditing;

use Illuminate\Support\Facades\App;

trait Recordable
{
    use \Altek\Accountant\Recordable;

    public function getAuditListUrl(): string
    {
        $resolver = App::make(AuditableClassResolver::class);
        $label = $resolver->getAuditListLabel($this);

        return route('front.panel.audits.index', ['label' => $label, 'key' => $this->getKey()]);
    }
}
