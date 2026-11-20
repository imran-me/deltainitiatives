<?php

return [
    App\Providers\AppServiceProvider::class,

    // Modules (order matters: Core and Admin first so others can register with them).
    Modules\Core\Providers\CoreServiceProvider::class,
    Modules\Admin\Providers\AdminServiceProvider::class,
    Modules\Users\Providers\UsersServiceProvider::class,
    Modules\CMS\Providers\CMSServiceProvider::class,
    Modules\IdeaPortal\Providers\IdeaPortalServiceProvider::class,
    Modules\Programmes\Providers\ProgrammesServiceProvider::class,
    Modules\Partnerships\Providers\PartnershipsServiceProvider::class,
    Modules\Applications\Providers\ApplicationsServiceProvider::class,
    Modules\Contact\Providers\ContactServiceProvider::class,
    Modules\Newsletter\Providers\NewsletterServiceProvider::class,
    Modules\Publishing\Providers\PublishingServiceProvider::class,
];
