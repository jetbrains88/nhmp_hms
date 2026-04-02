import re
import os

files = [
    'resources/views/admin/audit/index.blade.php',
    'resources/views/admin/users/index.blade.php',
    'resources/views/admin/roles/index.blade.php',
    'resources/views/admin/branches/index.blade.php',
    'resources/views/admin/permissions/index.blade.php',
]

def get_field_name(header_text):
    text = header_text.strip().lower()
    mapping = {
        'time stamp': 'created_at',
        'performed by': 'user_id',
        'target entity': 'entity_type',
        'action': 'action',
        'branch': 'branch_id',
        'details': None,
        'user info': 'name',
        'office': 'office_id',
        'type': 'type',
        'location': 'location',
        'contact': 'phone',
        'status': 'is_active',
        'actions': None,
        'role name': 'name',
        'system id': 'id',
        'permissions': None,
        'group': 'group',
        'permission access': 'name',
    }
    for k, v in mapping.items():
        if k in text: return v
    return None

def get_color_classes(icon_class):
    icon = icon_class.lower()
    if 'clock' in icon:
        return 'text-sky-500', 'bg-sky-50', 'border-sky-200'
    elif 'user' in icon:
        return 'text-indigo-500', 'bg-indigo-50', 'border-indigo-200'
    elif 'cube' in icon or 'layer' in icon or 'building' in icon:
        return 'text-purple-500', 'bg-purple-50', 'border-purple-200'
    elif 'tag' in icon or 'shield' in icon:
        return 'text-emerald-500', 'bg-emerald-50', 'border-emerald-200'
    elif 'branch' in icon or 'map' in icon or 'code-branch' in icon:
        return 'text-amber-500', 'bg-amber-50', 'border-amber-200'
    elif 'envelope' in icon or 'phone' in icon:
        return 'text-rose-500', 'bg-rose-50', 'border-rose-200'
    elif 'fingerprint' in icon or 'key' in icon:
        return 'text-indigo-500', 'bg-indigo-50', 'border-indigo-200'
    elif 'search' in icon or 'cogs' in icon:
        return 'text-slate-500', 'bg-slate-50', 'border-slate-200'
    return 'text-indigo-500', 'bg-indigo-50', 'border-indigo-200'

for filepath in files:
    full_path = os.path.join('/home/mrpirzado/projects/nhmp_hms', filepath)
    if not os.path.exists(full_path):
        print(f"Skipping {filepath}, not found")
        continue

    with open(full_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Update <script> Alpine Logic
    # Check if sortField already exists to avoid double injecting
    if "sortField:" not in content:
        # Inject sortField and sortDirection right after 'logs: [],' or 'users: [],' etc, or loading:
        content = re.sub(r'(loading:\s*(?:false|true),)', 
                         r'\1\n        sortField: \'id\',\n        sortDirection: \'desc\',', content, count=1)
        
        # Inject sortBy function
        sort_func = """
        sortBy(field) {
            if (this.sortField === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDirection = 'asc';
            }
            if(typeof this.applyFilters === 'function') {
                this.applyFilters();
            } else if(typeof this.fetchData === 'function') {
                this.fetchData();
            }
        },"""
        if "init()" in content:
            content = content.replace("init() {", sort_func + "\n\n        init() {")

    # 2. Update params generation
    # Search for URLSearchParams({ and inject sort/direction
    def inject_params(m):
        block = m.group(1)
        if 'sort:' not in block:
            return "URLSearchParams({\n                    sort: this.sortField,\n                    direction: this.sortDirection," + block
        return m.group(0)
    
    content = re.sub(r'URLSearchParams\(\{([\s\S]*?\}|[\s\S]*?)\)', inject_params, content)

    # 3. Process TH elements
    
    th_pattern = re.compile(r'<th\b[^>]*>(.*?)<\/th>', re.DOTALL)
    
    def process_th(m):
        th_inner = m.group(1)
        header_text_match = re.search(r'<\/div>([^<]+)<\/div>', th_inner)
        if not header_text_match:
            header_text_match = re.search(r'<\/div>\s*([^<]+?)\s*$', th_inner)
        if not header_text_match:
            header_text_match = re.search(r'i>.*?<\/div>\s*([^<]+?)\s*<\/div>', re.sub(r'\s+', ' ', th_inner))

        if not header_text_match:
            return m.group(0) # Do not touch if we can't parse

        raw_text = re.sub(r'<[^>]+>', '', th_inner).strip()
        field_name = get_field_name(raw_text)
        
        icon_match = re.search(r'<i class="([^"]+)">', th_inner)
        icon_class = icon_match.group(1) if icon_match else 'fas fa-cube'
        
        c_text, c_bg, c_border = get_color_classes(icon_class)

        # Build replacement
        alignment_class = "text-left"
        if "text-center" in m.group(0): alignment_class = "text-center"
        if "text-right" in m.group(0): alignment_class = "text-right"

        align_flex = "justify-start"
        if "text-center" in alignment_class: align_flex = "justify-center"
        if "text-right" in alignment_class: align_flex = "justify-end"

        click_attr = f'@click="sortBy(\'{field_name}\')"' if field_name else ""
        cursor_class = "cursor-pointer group hover:bg-slate-50 transition-colors" if field_name else ""

        title_text = re.sub(r'\s+', ' ', raw_text).strip()

        sort_icons = f'''
                                        <i class="fas fa-sort ml-1 opacity-0 group-hover:opacity-100 transition-opacity text-slate-300" x-show="sortField !== '{field_name}'"></i>
                                        <i class="fas fa-sort-up ml-1 {c_text}" x-show="sortField === '{field_name}' && sortDirection === 'asc'"></i>
                                        <i class="fas fa-sort-down ml-1 {c_text}" x-show="sortField === '{field_name}' && sortDirection === 'desc'"></i>''' if field_name else ""

        new_th = f'''<th class="px-5 py-5 {alignment_class} {cursor_class}" {click_attr}>
                                    <div class="flex items-center {align_flex} gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg {c_bg} flex items-center justify-center {c_text} shadow-sm border {c_border} transition-all">
                                            <i class="{icon_class}"></i>
                                        </div>
                                        <span>{title_text}</span>{sort_icons}
                                    </div>
                                </th>'''
        return new_th

    content = th_pattern.sub(process_th, content)

    # Tooltips logic: Find known truncation places or actions and add title="xxx"
    # Wait, tooltips exist on action buttons as `title: "View Edit"` etc. I'll make sure tooltips plugin (Tippy or simple browser title) is applied where `text-truncate` or `truncate` exists.
    content = re.sub(r'(class="[^"]*truncate[^"]*")(?!\s+title=)', r'\1 :title="..."', content)

    with open(full_path, 'w', encoding='utf-8') as f:
        f.write(content)

print("UI rewrite completed.")
