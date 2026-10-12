<?php

namespace App\Http\Controllers\Web\Site;

use App\Http\Controllers\Controller;
use App\Services\Site\SitePageService;
use Illuminate\View\View;

class SitePageController extends Controller
{
    public function __construct(
        private readonly SitePageService $sitePageService,
    ) {}

    public function home(): View
    {
        $site = $this->sitePageService->home();

        return view('landing.index', [
            'site' => $site,
            'activeNav' => 'home',
        ]);
    }

    public function about(): View
    {
        return view('site.about', [
            'site' => $this->sitePageService->about(),
            'activeNav' => 'about',
        ]);
    }

    public function shop(): View
    {
        return view('site.shop', [
            'site' => $this->sitePageService->shop(),
            'activeNav' => 'shop',
        ]);
    }

    public function sell(): View
    {
        return view('site.sell', [
            'site' => $this->sitePageService->sell(),
            'activeNav' => 'sell',
        ]);
    }

    public function sellers(): View
    {
        return view('site.sellers.index', [
            'site' => $this->sitePageService->sellers(),
            'activeNav' => 'sellers',
        ]);
    }

    public function sellerStore(string $slug): View
    {
        $site = $this->sitePageService->sellerStore($slug);

        abort_if($site === null, 404);

        return view('site.sellers.show', [
            'site' => $site,
            'activeNav' => 'sellers',
        ]);
    }

    public function cities(): View
    {
        return view('site.cities.index', [
            'site' => $this->sitePageService->cities(),
            'activeNav' => 'cities',
        ]);
    }

    public function cityShow(string $city): View
    {
        $site = $this->sitePageService->cityShow($city);

        abort_if($site === null, 404);

        return view('site.cities.show', [
            'site' => $site,
            'activeNav' => 'cities',
        ]);
    }

    public function blog(): View
    {
        return view('site.blog.index', [
            'site' => $this->sitePageService->blog(),
            'activeNav' => 'blog',
        ]);
    }

    public function privacy(): View
    {
        return view('site.privacy', [
            'site' => $this->sitePageService->privacy(),
            'activeNav' => null,
        ]);
    }

    public function terms(): View
    {
        return view('site.terms', [
            'site' => $this->sitePageService->terms(),
            'activeNav' => null,
        ]);
    }

    public function contact(): View
    {
        return view('site.contact', [
            'site' => $this->sitePageService->contact(),
            'activeNav' => 'contact',
        ]);
    }

    public function quote(): View
    {
        return view('site.quote', [
            'site' => $this->sitePageService->quote(),
            'activeNav' => 'quote',
        ]);
    }

    public function drive(): View
    {
        return view('site.drive', [
            'site' => $this->sitePageService->drive(),
            'activeNav' => 'drive',
        ]);
    }

    public function designRoom(): View
    {
        return view('site.design-room', [
            'site' => $this->sitePageService->designRoom(),
            'activeNav' => null,
        ]);
    }

    public function designMe(): View
    {
        return view('site.design-me', [
            'site' => $this->sitePageService->designMe(),
            'activeNav' => null,
        ]);
    }

    public function dashboard(): View
    {
        return view('site.portals.dashboard', [
            'portal' => $this->sitePageService->dashboard(),
        ]);
    }

    public function distribute(): View
    {
        return view('site.portals.distribute', [
            'portal' => $this->sitePageService->distribute(),
        ]);
    }

    public function admin(): View
    {
        return view('site.portals.admin', [
            'portal' => $this->sitePageService->admin(),
        ]);
    }
}
