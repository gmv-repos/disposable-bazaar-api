<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebsiteSetting;

class WebsiteSettingController extends Controller
{
    public function index()
    {
        $websiteSettings = WebsiteSetting::all();
        return view('adminPanel.website_settings.index', compact('websiteSettings'));
    }
}
