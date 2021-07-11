<?php

namespace App\Library\Auditing;

use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\Donation;
use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Library\Auditing\Contracts\Recordable;
use App\Library\Auditing\Exceptions\UnresolvableRecordableClassException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuditableClassResolver
{
    /**
     * A mapping of URL strings to classes.
     *
     * @var array|string[]
     */
    private array $inspectionLabels = [
        'account' => Account::class,
        'donation' => Donation::class,
        'donation_perk' => DonationPerk::class,
        'minecraft_player' => MinecraftPlayer::class,
    ];

    private array $reversedLabels = [];

    /**
     * AuditableClassResolver constructor.
     */
    public function __construct()
    {
        $this->reversedLabels = array_flip($this->inspectionLabels);
    }

    /**
     * Resolve an audit class human label to its class.
     */
    public function resolveLabelToClass(string $label): string
    {
        if (! in_array($label, array_keys($this->inspectionLabels))) {
            throw new NotFoundHttpException();
        }

        return $this->inspectionLabels[$label];
    }

    /**
     * @throws UnresolvableRecordableClassException
     */
    public function getAuditListLabel(Recordable $recordable): string
    {
        $className = get_class($recordable);
        if (array_key_exists($className, $this->reversedLabels)) {
            return $this->reversedLabels[$className];
        } else {
            throw new UnresolvableRecordableClassException('Unable to resolve class '.$className);
        }
    }
}
