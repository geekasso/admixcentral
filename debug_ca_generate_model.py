import json
try:
    with open('openapi.json', 'r') as f:
        data = json.load(f)
    
    if 'components' in data and 'schemas' in data['components'] and 'CertificateAuthorityGenerate' in data['components']['schemas']:
        print(json.dumps(data['components']['schemas']['CertificateAuthorityGenerate'], indent=2))
    else:
        print("Schema not found")
except Exception as e:
    print(e)
