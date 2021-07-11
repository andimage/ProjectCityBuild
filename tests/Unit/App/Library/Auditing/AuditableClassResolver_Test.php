<?php

namespace Tests\Unit\App\Library\Auditing;

use App\Entities\Accounts\Models\Account;
use App\Library\Auditing\AuditableClassResolver;
use App\Library\Auditing\Contracts\Recordable;
use App\Library\Auditing\Exceptions\UnresolvableRecordableClassException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class AuditableClassResolver_Test extends TestCase
{
    public function testLabelCanBeResolvedToClass()
    {
        $resolver = new AuditableClassResolver();
        $class = $resolver->resolveLabelToClass('account');
        $this->assertEquals('App\Entities\Accounts\Models\Account', $class);
    }

    public function testCanGetLabelForClass()
    {
        $resolver = new AuditableClassResolver();
        $account = Account::factory()->make();
        $label = $resolver->getAuditListLabel($account);
        $this->assertEquals('account', $label);
    }

    public function testResolvingNonexistentLabel()
    {
        $resolver = new AuditableClassResolver();
        $this->expectException(NotFoundHttpException::class);
        $resolver->resolveLabelToClass('foo');
    }

    public function testGetLabelForUnknownAuditableClass()
    {
        $resolver = new AuditableClassResolver();

        $this->expectException(UnresolvableRecordableClassException::class);
        $resolver->getAuditListLabel(new FakeAuditable());
    }
}

class FakeAuditable implements Recordable
{
    use \App\Library\Auditing\Recordable;

    public function getMorphClass()
    {
        return '';
    }

    public function getPanelShowUrl(): string
    {
        return '/';
    }
}
