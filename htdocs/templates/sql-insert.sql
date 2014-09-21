INSERT INTO {{ table_name }}
({{# col_names }}{{#cols}}{{ . }},{{/cols}}{{tcol}}{{/col_names}})
VALUES 
{{# row_items }}
{{# rows}}({{# values }}{{# vals }}{{.}},{{/vals}}{{tval}}{{/values}}),{{/rows}}
{{# trow }}({{# values }}{{# vals }}{{.}},{{/vals}}{{tval}}{{/values}}){{/ trow }}
{{/ row_items }}
