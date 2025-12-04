<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class CertificateManagerController extends Controller
{
    public function index(Firewall $firewall, Request $request)
    {
        $api = new PfSenseApiService($firewall);
        $tab = $request->query('tab', 'cas');
        $data = [];
        $error = null;

        try {
            if ($tab === 'cas') {
                $response = $api->getCertificateAuthorities();
                $data['cas'] = $response['data'] ?? [];
            } elseif ($tab === 'certificates') {
                $response = $api->getCertificates();
                $data['certificates'] = $response['data'] ?? [];
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return view('system.certificate_manager.index', compact('firewall', 'tab', 'data', 'error'));
    }

    public function createCa(Firewall $firewall)
    {
        return view('system.certificate_manager.cas.create', compact('firewall'));
    }

    public function storeCa(Firewall $firewall, Request $request)
    {
        $api = new PfSenseApiService($firewall);
        $method = $request->input('method');
        $data = $request->except(['_token', 'method']);

        try {
            if ($method === 'internal') {
                $api->generateCertificateAuthority($data);
            } else {
                $api->createCertificateAuthority($data);
            }
            return redirect()->route('system.certificate_manager.index', ['firewall' => $firewall, 'tab' => 'cas'])
                ->with('success', 'Certificate Authority created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create CA: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyCa(Firewall $firewall, string $id)
    {
        $api = new PfSenseApiService($firewall);
        try {
            $api->deleteCertificateAuthority($id);
            return redirect()->route('system.certificate_manager.index', ['firewall' => $firewall, 'tab' => 'cas'])
                ->with('success', 'Certificate Authority deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete CA: ' . $e->getMessage());
        }
    }

    public function createCert(Firewall $firewall)
    {
        $api = new PfSenseApiService($firewall);
        $cas = [];
        try {
            $response = $api->getCertificateAuthorities();
            $cas = $response['data'] ?? [];
        } catch (\Exception $e) {
            // Ignore error, just empty CAs
        }
        return view('system.certificate_manager.certificates.create', compact('firewall', 'cas'));
    }

    public function storeCert(Firewall $firewall, Request $request)
    {
        $api = new PfSenseApiService($firewall);
        $method = $request->input('method');
        $data = $request->except(['_token', 'method']);

        try {
            if ($method === 'internal') {
                $api->generateCertificate($data);
            } else {
                $api->createCertificate($data);
            }
            return redirect()->route('system.certificate_manager.index', ['firewall' => $firewall, 'tab' => 'certificates'])
                ->with('success', 'Certificate created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create Certificate: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyCert(Firewall $firewall, string $id)
    {
        $api = new PfSenseApiService($firewall);
        try {
            $api->deleteCertificate($id);
            return redirect()->route('system.certificate_manager.index', ['firewall' => $firewall, 'tab' => 'certificates'])
                ->with('success', 'Certificate deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete Certificate: ' . $e->getMessage());
        }
    }
}
