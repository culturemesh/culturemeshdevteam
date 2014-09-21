UPDATE {{ table_name }}
SET 
{{# nv }}
{{ col }} = {{ value }},
{{/ nv }}
{{# terminal}}
{{ col }} = {{ value }}
{{/ terminal }}
WHERE 
{{# param }}
{{ fp }} = {{ fp_val }}
{{/ param }}
{{# more }}
AND {{ mfp }} = {{ mfp_val }}
{{/ more }}
