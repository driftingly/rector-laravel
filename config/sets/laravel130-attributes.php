<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

use RectorLaravel\Rector\Class_\AliasesPropertyToAliasesAttributeRector;
use RectorLaravel\Rector\Class_\CommandHiddenPropertyToHiddenAttributeRector;
use RectorLaravel\Rector\Class_\DescriptionPropertyToDescriptionAttributeRector;
use RectorLaravel\Rector\Class_\HelpPropertyToHelpAttributeRector;
use RectorLaravel\Rector\Class_\SignaturePropertyToSignatureAttributeRector;

use RectorLaravel\Rector\Class_\AppendsPropertyToAppendsAttributeRector;
use RectorLaravel\Rector\Class_\BackoffPropertyToBackoffAttributeRector;
use RectorLaravel\Rector\Class_\CollectedByPropertyToCollectedByAttributeRector;
use RectorLaravel\Rector\Class_\ConnectionPropertyToConnectionAttributeRector;
use RectorLaravel\Rector\Class_\DateFormatPropertyToDateFormatAttributeRector;
use RectorLaravel\Rector\Class_\FillablePropertyToFillableAttributeRector;
use RectorLaravel\Rector\Class_\GuardedPropertyToGuardedAttributeRector;
use RectorLaravel\Rector\Class_\HiddenPropertyToHiddenAttributeRector;
use RectorLaravel\Rector\Class_\TablePropertyToTableAttributeRector;
use RectorLaravel\Rector\Class_\TouchesPropertyToTouchesAttributeRector;
use RectorLaravel\Rector\Class_\VisiblePropertyToVisibleAttributeRector;
use RectorLaravel\Rector\Class_\WithoutIncrementingPropertyToWithoutIncrementingAttributeRector;
use RectorLaravel\Rector\Class_\WithoutTimestampsPropertyToWithoutTimestampsAttributeRector;

use RectorLaravel\Rector\Class_\CollectsPropertyToCollectsAttributeRector;
use RectorLaravel\Rector\Class_\PreserveKeysPropertyToPreserveKeysAttributeRector;

use RectorLaravel\Rector\Class_\ErrorBagPropertyToErrorBagAttributeRector;
use RectorLaravel\Rector\Class_\StopOnFirstFailurePropertyToStopOnFirstFailureAttributeRector;

use RectorLaravel\Rector\Class_\DelayPropertyToDelayAttributeRector;
use RectorLaravel\Rector\Class_\DeleteWhenMissingModelsPropertyToDeleteWhenMissingModelsAttributeRector;
use RectorLaravel\Rector\Class_\FailOnTimeoutPropertyToFailOnTimeoutAttributeRector;
use RectorLaravel\Rector\Class_\JobConnectionPropertyToJobConnectionAttributeRector;
use RectorLaravel\Rector\Class_\MaxExceptionsPropertyToMaxExceptionsAttributeRector;
use RectorLaravel\Rector\Class_\QueuePropertyToQueueAttributeRector;
use RectorLaravel\Rector\Class_\TimeoutPropertyToTimeoutAttributeRector;
use RectorLaravel\Rector\Class_\TriesPropertyToTriesAttributeRector;
use RectorLaravel\Rector\Class_\UniqueForPropertyToUniqueForAttributeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    // Console
    $rectorConfig->rule(AliasesPropertyToAliasesAttributeRector::class);
    $rectorConfig->rule(CommandHiddenPropertyToHiddenAttributeRector::class);
    $rectorConfig->rule(DescriptionPropertyToDescriptionAttributeRector::class);
    $rectorConfig->rule(HelpPropertyToHelpAttributeRector::class);
    $rectorConfig->rule(SignaturePropertyToSignatureAttributeRector::class);

    // Eloquent
    $rectorConfig->rule(AppendsPropertyToAppendsAttributeRector::class);
    $rectorConfig->rule(CollectedByPropertyToCollectedByAttributeRector::class);
    $rectorConfig->rule(ConnectionPropertyToConnectionAttributeRector::class);
    $rectorConfig->rule(DateFormatPropertyToDateFormatAttributeRector::class);
    $rectorConfig->rule(FillablePropertyToFillableAttributeRector::class);
    $rectorConfig->rule(GuardedPropertyToGuardedAttributeRector::class);
    $rectorConfig->rule(HiddenPropertyToHiddenAttributeRector::class);
    $rectorConfig->rule(TablePropertyToTableAttributeRector::class);
    $rectorConfig->rule(TouchesPropertyToTouchesAttributeRector::class);
    $rectorConfig->rule(VisiblePropertyToVisibleAttributeRector::class);
    $rectorConfig->rule(WithoutIncrementingPropertyToWithoutIncrementingAttributeRector::class);
    $rectorConfig->rule(WithoutTimestampsPropertyToWithoutTimestampsAttributeRector::class);

    // API Resource
    $rectorConfig->rule(CollectsPropertyToCollectsAttributeRector::class);
    $rectorConfig->rule(PreserveKeysPropertyToPreserveKeysAttributeRector::class);

    // Form Request
    $rectorConfig->rule(ErrorBagPropertyToErrorBagAttributeRector::class);
    $rectorConfig->rule(StopOnFirstFailurePropertyToStopOnFirstFailureAttributeRector::class);

    // Queue
    $rectorConfig->rule(BackoffPropertyToBackoffAttributeRector::class);
    $rectorConfig->rule(DelayPropertyToDelayAttributeRector::class);
    $rectorConfig->rule(DeleteWhenMissingModelsPropertyToDeleteWhenMissingModelsAttributeRector::class);
    $rectorConfig->rule(FailOnTimeoutPropertyToFailOnTimeoutAttributeRector::class);
    $rectorConfig->rule(JobConnectionPropertyToJobConnectionAttributeRector::class);
    $rectorConfig->rule(MaxExceptionsPropertyToMaxExceptionsAttributeRector::class);
    $rectorConfig->rule(QueuePropertyToQueueAttributeRector::class);
    $rectorConfig->rule(TimeoutPropertyToTimeoutAttributeRector::class);
    $rectorConfig->rule(TriesPropertyToTriesAttributeRector::class);
    $rectorConfig->rule(UniqueForPropertyToUniqueForAttributeRector::class);
};
