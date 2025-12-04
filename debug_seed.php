try {
echo "Creating Company...\n";
$company = \App\Models\Company::create([
'name' => 'Demo Company',
'description' => 'A demo company for testing.',
]);
echo "Company ID: " . $company->id . "\n";

echo "Creating Admin...\n";
\App\Models\User::create([
'name' => 'Global Admin',
'email' => 'admin@admixcentral.com',
'password' => bcrypt('password'),
'role' => 'admin',
]);
echo "Admin Created\n";

echo "Creating Firewall...\n";
\App\Models\Firewall::create([
'company_id' => $company->id,
'name' => 'pfSense Lab',
'url' => 'https://172.30.1.129:444',
'api_key' => 'admin',
'api_secret' => 'pfsense',
'description' => 'Lab firewall instance',
]);
echo "Firewall Created\n";
} catch (\Exception $e) {
echo "Error: " . $e->getMessage() . "\n";
}