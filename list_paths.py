import json

try:
    with open('openapi.json', 'r') as f:
        data = json.load(f)
        with open('paths_output.txt', 'w') as out:
            paths = data.get('paths', {})
            if '/api/v2/firewall/alias' in paths:
                out.write("Methods for /api/v2/firewall/alias:\n")
                out.write(str(paths['/api/v2/firewall/alias'].keys()) + "\n")
                if 'delete' in paths['/api/v2/firewall/alias']:
                    out.write("DELETE parameters:\n")
                    out.write(str(paths['/api/v2/firewall/alias']['delete'].get('parameters', [])) + "\n")
            else:
                out.write("/api/v2/firewall/alias not found\n")
except Exception as e:
    with open('paths_output.txt', 'w') as out:
        out.write(f"Error: {e}")
