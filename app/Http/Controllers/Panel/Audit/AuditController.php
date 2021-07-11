<?php

namespace App\Http\Controllers\Panel\Audit;

use Altek\Accountant\Models\Ledger;
use App\Http\WebController;
use App\Library\Auditing\AuditableClassResolver;

class AuditController extends WebController
{
    private function humanLabel($model, $key): string
    {
        return class_basename(get_class($model)).' #'.$key;
    }

    public function index(string $label, string $key, AuditableClassResolver $resolver)
    {
        $auditingClass = $resolver->resolveLabelToClass($label);
        $auditingModel = $auditingClass::findOrFail($key);
        $ledgers = $auditingModel->ledgers()->with('user')->latest()->get();
        $humanLabel = $this->humanLabel($auditingModel, $key);

        return view('admin.auditing.index')->with(compact('ledgers', 'auditingModel', 'humanLabel'));
    }

    public function show(Ledger $ledger)
    {
        $ledger->load('user');

        return view('admin.auditing.show')->with(compact('ledger'));
    }
}
