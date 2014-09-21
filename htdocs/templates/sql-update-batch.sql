UPDATE {{ table_name }}
SET {{ change_col }} = CASE 
{{# batch_vals }}
WHEN {{ id_col }} THEN {{ change_val}}
{{/ batch_vals }}
ELSE {{ change_col }}
END
{{# mcols }}
,{{ mchange_col}} = CASE
{{# mbatch_vals }}
WHEN {{ id_col }} THEN {{ mchange_val }}
{{/ mbatch_vals }}
ELSE {{ mchange_col }}
END
{{/ mcols }}
