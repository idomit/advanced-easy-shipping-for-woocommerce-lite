( function ($) {
	'use strict';
	function awsAllowSpecialCharacter (str) {
		return str.replace('&#8211;', '–').replace('&gt;', '>').replace('&lt;', '<').replace('&#197;', 'Å');
	}
	
	function commonMultiFilter (getClassName, ajaxFunction) {
		jQuery(getClassName).each(function () {
			jQuery(getClassName).select2({
				ajax: {
					url: aws_var.ajaxurl,
					dataType: 'json',
					delay: 250,
					data: function (params) {
						return {
							value: params.term,
							action: ajaxFunction
						};
					},
					processResults: function (data) {
						var options = [];
						if (data) {
							$.each(data, function (index, text) {
								options.push({ id: text[0], text: awsAllowSpecialCharacter(text[1]) });
							});
						}
						return {
							results: options
						};
					},
					cache: true
				},
				minimumInputLength: 3
			});
		});
	}
	
	function checkAllFilter () {
		jQuery('.tr_clone .asw_sub_div .asw_condition_nt_rmd_data').each(function () {
			var value_based_on_cc_change = $(this).val();
			var rule_name = $(this).parent().parent().parent().parent().parent().parent().attr('data-gb');
			var main_tr_id = $(this).parent().parent().attr('id');
			if ('cc_subtotal_af_disc' === value_based_on_cc_change || 'cc_country' === value_based_on_cc_change || 'cc_products' === value_based_on_cc_change) {
				var data_sel_attr = getOtDynamicValue(value_based_on_cc_change);
				if ('cb' === rule_name) {
					commonMultiFilter('.asw_' + rule_name + '_' + data_sel_attr + '_condition_value_cls', 'aws_get_value_based_on_' + data_sel_attr);
				}
				if ('lb' === rule_name || 'cs' === rule_name) {
					jQuery('.asw_' + rule_name + '_' + data_sel_attr + '_condition_value_cls').select2();
				}
			}
		});
		
	}
	
	function asw_get_country_list (getSelectElement) {
		var country_list = JSON.parse(aws_var.country_obj);
		for (var option in country_list) {
			var $option = document.createElement('option');
			$option.setAttribute('value', option);
			var $option_texnode = document.createTextNode(country_list[option]);
			$option.appendChild($option_texnode);
			getSelectElement.appendChild($option);
		}
		return getSelectElement;
	}
	
	function commonLocationFilter (rule_name, value_based_on_cc_change, main_tr_id) {
		var getSelectElement = document.getElementById('asw_' + rule_name + '_condition_value_select_id_' + main_tr_id);
		if ('cc_country' === value_based_on_cc_change) {
			asw_get_country_list(getSelectElement);
		}
	}
	
	function removeMinusButton () {
		var current_tr_id = jQuery('.asw-extra-rule-div tr').length;
		if (current_tr_id < 2) {
			jQuery('.asw_condition_remove').remove();
		}
	}
	
	function ChangeOperatorData (element_id, append_element, staticSelectAttr, action_type) {
		var type_of_field = JSON.parse(aws_var.type_of_field);
		var field_type;
		if ('new' == action_type) {
			field_type = type_of_field['cc_' + staticSelectAttr];
		} else {
			field_type = type_of_field[staticSelectAttr];
		}
		element_id = jQuery.trim(element_id);
		jQuery('#' + element_id + ' option').remove();
		var operator_array_more = JSON.parse(aws_var.conditional_op_more);
		if (operator_array_more) {
			var i = 0;
			for (var operator_opt_more in operator_array_more) {
				if (field_type != 'input' && i == 2) {
					break;
				}
				var $option_more = document.createElement('option');
				$option_more.setAttribute('value', operator_opt_more);
				var $option_textnode_more = document.createTextNode(operator_array_more[operator_opt_more]);
				$option_more.appendChild($option_textnode_more);
				append_element.appendChild($option_more);
				i++;
			}
		}
		return append_element;
	}
	
	function getTotalTRLength () {
		var total_tr_id = jQuery('.asw-extra-rule-div tr').length;
		return total_tr_id;
	}
	
	function onlyAppendTR (this_var, rule_name) {
		var unique_name = '';
		if ('asw_cart_rule_status' == this_var.attr('name')) {
			unique_name = 'products';
		}
		
		var $tr = document.createElement('tr');
		$tr.setAttribute('id', '0');
		$tr.setAttribute('class', 'tr_clone');
		
		var staticSelectAttr = '';
		var selectOptionData;
		if ('cb' === rule_name) {
			selectOptionData = '';
			staticSelectAttr = 'products';
		} else if ('lb' === rule_name) {
			selectOptionData = JSON.parse(aws_var.country_obj);
			staticSelectAttr = 'country';
		} else if ('cs' === rule_name) {
			selectOptionData = '';
			staticSelectAttr = 'subtotal_af_disc';
		} else if ('ps' === rule_name) {
			selectOptionData = '';
			staticSelectAttr = 'cc_tc_spec';
		}
		
		if ('us' == rule_name) {
			var $first_td = document.createElement('td');
			var $first_label = document.createElement('label');
			var $first_label_textnode = document.createTextNode('Available in Pro');
			$first_label.appendChild($first_label_textnode);
			$first_td.appendChild($first_label);
			$tr.appendChild($first_td);
			/*End Fourth TD*/
			return $tr;
			return false;
		}
		
		
		/*Start First TD*/
		var $first_td = document.createElement('td');
		$first_td.setAttribute('id', 'asw_' + rule_name + '_condition_name_div_0');
		$first_td.setAttribute('class', 'asw_' + rule_name + '_condition_name_div asw_sub_div');
		
		var $first_td_select = document.createElement('select');
		$first_td_select.setAttribute('id', 'asw_' + rule_name + '_condition_name_div_0');
		$first_td_select.setAttribute('class', 'asw_' + rule_name + '_condition_nt_rmd_data asw_condition_nt_rmd_data');
		$first_td_select.setAttribute('name', 'asw_condition_name[' + rule_name + '][0][condition]');
		
		var select_option_array = [];
		if ('cb' === rule_name) {
			select_option_array = JSON.parse(aws_var.cart_option);
		} else if ('lb' === rule_name) {
			select_option_array = JSON.parse(aws_var.location_option);
		} else if ('cs' === rule_name) {
			select_option_array = JSON.parse(aws_var.cart_specific_option);
		} else if ('ps' === rule_name) {
			select_option_array = JSON.parse(aws_var.product_option);
		}
		if (select_option_array) {
			for (var sel_option in select_option_array) {
				var $option = document.createElement('option');
				$option.setAttribute('value', sel_option);
				if (( 'cc_subtotal_af_disc' != sel_option && 'cs' == rule_name ) ||
				    ( 'cc_country' != sel_option && 'lb' == rule_name ) ||
				    ( 'cc_products' != sel_option && 'cb' == rule_name ) ||
				    ( 'cc_tc_spec' != sel_option && 'ps' == rule_name ) ) {
					$option.setAttribute('disabled', 'disabled');
				}
				var $option_texnode = document.createTextNode(select_option_array[sel_option]);
				$option.appendChild($option_texnode);
				$first_td_select.appendChild($option);
			}
		}
		$first_td.appendChild($first_td_select);
		$tr.appendChild($first_td);
		/*End First TD*/
		
		if ('ps' !== rule_name) {
			/*Start Second TD*/
			var $second_td = document.createElement('td');
			$second_td.setAttribute('id', 'asw_' + rule_name + '_operator_id_0');
			$second_td.setAttribute('class', 'asw_' + rule_name + '_operator_div asw_sub_div');
			
			var $second_td_select = document.createElement('select');
			$second_td_select.setAttribute('id', 'asw_' + rule_name + '_operator_select_id_0');
			$second_td_select.setAttribute('class', 'asw_' + rule_name + '_operator_nt_rmd_data asw_operator_nt_rmd_data');
			$second_td_select.setAttribute('name', 'asw_condition_name[' + rule_name + '][0][operator]');
			
			$second_td_select = ChangeOperatorData('asw_' + rule_name + '_operator_select_id_0', $second_td_select, staticSelectAttr, 'new');
			
			$second_td.appendChild($second_td_select);
			$tr.appendChild($second_td);
			/*End Second TD*/
		}
		
		/*Start Third TD*/
		var $third_td = document.createElement('td');
		$third_td.setAttribute('id', 'asw_' + rule_name + '_condition_value_id_0');
		$third_td.setAttribute('class', 'asw_' + rule_name + '_condition_value_div asw_sub_div');
		console.log('select_option_array)[0] ' + Object.keys(select_option_array)[0]);
		aswarrayOfFieldType(Object.keys(select_option_array)[0], rule_name, staticSelectAttr, '0', $third_td, selectOptionData);
		$tr.appendChild($third_td);
		/*End Third TD*/
		
		if ('ps' === rule_name) {
			/*Start TD for ps condition apply per unit*/
			var $td_apu = document.createElement('td');
			$td_apu.setAttribute('id', 'asw_' + rule_name + '_apu_id_0');
			$td_apu.setAttribute('class', 'asw_' + rule_name + '_apu_div asw_sub_div');
			
			var $td_apu_elem = document.createElement('input');
			$td_apu_elem.setAttribute('type', 'checkbox');
			$td_apu_elem.setAttribute('id', 'asw_' + rule_name + '_apu_select_id_0');
			$td_apu_elem.setAttribute('class', 'asw_' + rule_name + '_apu_nt_rmd_data asw_apu_nt_rmd_data');
			$td_apu_elem.setAttribute('name', 'asw_condition_name[' + rule_name + '][0][advanced][apu]');
			$td_apu.appendChild($td_apu_elem);
			
			var $td_apu_label = document.createElement('label');
			$td_apu_label.setAttribute('for', 'asw_' + rule_name + '_apu_select_id_0');
			var $td_apu_textnode = document.createTextNode('Cost Per');
			$td_apu_label.appendChild($td_apu_textnode);
			$td_apu.appendChild($td_apu_label);
			
			$tr.appendChild($td_apu);
			/*End TD for ps condition apply per unit*/
			
			/*Start TD for ps condition unit type*/
			var $td_ut = document.createElement('td');
			$td_ut.setAttribute('id', 'asw_' + rule_name + '_ut_id_0');
			$td_ut.setAttribute('class', 'asw_' + rule_name + '_ut_div asw_sub_div');
			
			var $td_ut_elem = document.createElement('select');
			$td_ut_elem.setAttribute('name', 'asw_condition_name[' + rule_name + '][0][advanced][ut]');
			
			var $ut_data_array = JSON.parse(aws_var.per_unit_option);
			if ($ut_data_array) {
				for (var ut_option in $ut_data_array) {
					var $option = document.createElement('option');
					$option.setAttribute('value', ut_option);
					console.log('ut_option ' + ut_option);
					if ('st_without_tax_disc' != ut_option &&
					    'weight' != ut_option) {
						console.log('if');
						$option.setAttribute('disabled', 'disabled');
					}
					var $option_texnode = document.createTextNode($ut_data_array[ut_option]);
					$option.appendChild($option_texnode);
					$td_ut_elem.appendChild($option);
				}
			}
			$td_ut.appendChild($td_ut_elem);
			
			$tr.appendChild($td_ut);
			/*End Second TD for ps condition unit type*/
			
			/*Start TD for ps condition min value*/
			var $td_minval = document.createElement('td');
			$td_minval.setAttribute('id', 'asw_' + rule_name + '_minval_id_0');
			$td_minval.setAttribute('class', 'asw_' + rule_name + '_minval_div asw_sub_div');
			
			var $td_minval_elem = document.createElement('input');
			$td_minval_elem.setAttribute('type', 'textbox');
			$td_minval_elem.setAttribute('id', 'asw_' + rule_name + '_minval_select_id_0');
			$td_minval_elem.setAttribute('class', 'asw_' + rule_name + '_minval_nt_rmd_data asw_minval_nt_rmd_data');
			$td_minval_elem.setAttribute('name', 'asw_condition_name[' + rule_name + '][0][advanced][minval]');
			$td_minval_elem.setAttribute('placeholder', 'Min Value');
			$td_minval_elem.setAttribute('value', '');
			$td_minval.appendChild($td_minval_elem);
			
			$tr.appendChild($td_minval);
			/*End TD for ps condition min value*/
			
			/*Start TD for ps condition max value*/
			var $td_maxval = document.createElement('td');
			$td_maxval.setAttribute('id', 'asw_' + rule_name + '_maxval_id_0');
			$td_maxval.setAttribute('class', 'asw_' + rule_name + '_maxval_div asw_sub_div');
			
			var $td_maxval_elem = document.createElement('input');
			$td_maxval_elem.setAttribute('type', 'textbox');
			$td_maxval_elem.setAttribute('id', 'asw_' + rule_name + '_maxval_select_id_0');
			$td_maxval_elem.setAttribute('class', 'asw_' + rule_name + '_maxval_nt_rmd_data asw_maxval_nt_rmd_data');
			$td_maxval_elem.setAttribute('name', 'asw_condition_name[' + rule_name + '][0][advanced][maxval]');
			$td_maxval_elem.setAttribute('placeholder', 'Max Value');
			$td_maxval_elem.setAttribute('value', '');
			$td_maxval.appendChild($td_maxval_elem);
			
			$tr.appendChild($td_maxval);
			/*End TD for ps condition max value*/
			
			/*Start TD for ps condition price*/
			var $td_price = document.createElement('td');
			$td_price.setAttribute('id', 'asw_' + rule_name + '_price_id_0');
			$td_price.setAttribute('class', 'asw_' + rule_name + '_price_div asw_sub_div');
			
			var $td_price_elem = document.createElement('input');
			$td_price_elem.setAttribute('type', 'textbox');
			$td_price_elem.setAttribute('id', 'asw_' + rule_name + '_price_select_id_0');
			$td_price_elem.setAttribute('class', 'asw_' + rule_name + '_price_nt_rmd_data asw_price_nt_rmd_data');
			$td_price_elem.setAttribute('name', 'asw_condition_name[' + rule_name + '][0][advanced][price]');
			$td_price_elem.setAttribute('placeholder', 'Amount');
			$td_price_elem.setAttribute('value', '');
			$td_price.appendChild($td_price_elem);
			
			$tr.appendChild($td_price);
			/*End TD for ps condition price*/
		}
		
		/*Start Fourth TD*/
		var $fourth_td = document.createElement('td');
		$fourth_td.setAttribute('id', 'asw_' + rule_name + '_condition_add_id');
		$fourth_td.setAttribute('class', 'asw_condition_add');
		
		var $fourth_td_a = document.createElement('a');
		$fourth_td_a.setAttribute('class', 'button add-aws-condition');
		$fourth_td_a.setAttribute('data-group', '0');
		$fourth_td_a.setAttribute('href', 'javascript:void(0);');
		
		var $fourth_td_a_textnode = document.createTextNode('+');
		$fourth_td_a.appendChild($fourth_td_a_textnode);
		
		$fourth_td.appendChild($fourth_td_a);
		$tr.appendChild($fourth_td);
		/*End Fourth TD*/
		
		return $tr;
	}
	
	function addNewTRIfBlank (this_var, rule_name, total_tr_id, new_tr_inside_table_length) {
		if (new_tr_inside_table_length == 0) {
			var $tr = document.createElement('tr');
			$tr.setAttribute('class', 'asw-extra-rule');
			$tr.setAttribute('id', 'asw_' + rule_name + '_rule_options');
			$tr.setAttribute('data-gb', rule_name);
			
			var $tr_td = document.createElement('td');
			$tr_td.setAttribute('class', 'tbl_td');
			
			var $tr_td_table = document.createElement('table');
			$tr_td_table.setAttribute('class', 'asw-extra-rule-div');
			
			var $tr_td_table_body = document.createElement('tbody');
			
			var get_return_tr = onlyAppendTR(this_var, rule_name);
			
			$tr_td_table_body.append(get_return_tr);
			$tr_td_table.appendChild($tr_td_table_body);
			$tr_td.appendChild($tr_td_table);
			$tr.appendChild($tr_td);
			
			this_var.parent().parent().closest('tr').after($tr);
		}
		checkAllFilter();
	}
	
	function checkCHKcheckedThenLoadTR () {
		jQuery('.asw-tbl-cls .asw_rule_chk_status').each(function () {
			if ($(this).is(':checked')) {
				$(this).parent().parent().closest().show();
				var new_tr_inside_table_length = jQuery('.asw-extra-rule').length;
				var total_tr_id = jQuery('.asw-extra-rule-div tr').length;
				addNewTRIfBlank($(this), total_tr_id, new_tr_inside_table_length);
			} else {
				$(this).parent().parent().closest().hide();
			}
		});
	}
	
	function getOtDynamicValue (sel_val) {
		var data_sel_attr = sel_val.split('cc_');
		return data_sel_attr[1];
	}
	
	function createElementBasedOnType (inputType, rule_name, data_sel_attr, main_tr_id, selectOptionData, value_based_on_cc_change) {
		console.log('data_sel_attr ' + data_sel_attr);
		console.log('inputType ' + inputType);
		var element_type = document.createElement(inputType);
		if ('subtotal_af_disc' === data_sel_attr ||
		    'country' === data_sel_attr ||
		    'products' === data_sel_attr ||
		    'cc_tc_spec' === data_sel_attr) {
			var get_cc_name = getOtDynamicValue(value_based_on_cc_change);
			var get_placeholder = JSON.parse(aws_var.placeholder_arr);
			if (inputType == 'input') {
				element_type.setAttribute('type', 'text');
				element_type.setAttribute('name', 'asw_condition_name[' + rule_name + '][' + main_tr_id + '][value]');
				element_type.setAttribute('id', 'asw_' + rule_name + '_condition_value_select_id_' + main_tr_id);
				element_type.setAttribute('class', 'asw-td-input-field');
				element_type.setAttribute('value', '');
				element_type.setAttribute('data-sel-attr', get_cc_name);
				if (data_sel_attr) {
					element_type.setAttribute('placeholder', get_placeholder[data_sel_attr]);
				}
			}
			if (inputType == 'select') {
				element_type.setAttribute('name', 'asw_condition_name[' + rule_name + '][' + main_tr_id + '][value][]');
				element_type.setAttribute('id', 'asw_' + rule_name + '_condition_value_select_id_' + main_tr_id);
				element_type.setAttribute('class', 'asw_' + rule_name + '_' + data_sel_attr + '_condition_value_cls multiselect2');
				element_type.setAttribute('multiple', 'multiple');
				if (data_sel_attr) {
					element_type.setAttribute('data-placeholder', get_placeholder[data_sel_attr]);
				}
				if (selectOptionData) {
					if ('country' == data_sel_attr) {
						asw_get_country_list(element_type);
					} else {
						for (var option in selectOptionData) {
							var $option = document.createElement('option');
							$option.setAttribute('value', option);
							var $option_texnode = document.createTextNode(selectOptionData[option]);
							$option.appendChild($option_texnode);
							element_type.appendChild($option);
						}
					}
				}
			}
			if (inputType == 'textarea') {
				element_type.setAttribute('name', 'asw_condition_name[' + rule_name + '][' + main_tr_id + '][value]');
				element_type.setAttribute('id', 'asw_' + rule_name + '_condition_value_select_id_' + main_tr_id);
				element_type.setAttribute('class', 'asw-td-input-field asw-td-textarea-field');
				if (data_sel_attr) {
					element_type.setAttribute('placeholder', get_placeholder[data_sel_attr]);
				}
			}
			if (inputType == 'label') {
				element_type.setAttribute('for', 'asw_' + rule_name + '_condition_value_label_id_' + main_tr_id);
				element_type.setAttribute('class', 'asw-td-label-field');
				var createLableTextnode = document.createTextNode('Total Cart');
				
				element_type.setAttribute('type', 'hidden');
				element_type.setAttribute('name', 'asw_condition_name[' + rule_name + '][' + main_tr_id + '][value]');
				element_type.setAttribute('id', 'asw_' + rule_name + '_condition_value_select_id_' + main_tr_id);
				element_type.setAttribute('value', 'Total Cart');
				
				element_type.appendChild(createLableTextnode);
			}
		}
		
		return element_type;
	}
	
	function aswarrayOfFieldType (value_based_on_cc_change, rule_name, data_sel_attr, main_tr_id, where_append_position, selectOptionData) {
		console.log('value_based_on_cc_change ' + value_based_on_cc_change)
		var type_of_field = JSON.parse(aws_var.type_of_field);
		var $create_element;
		if (type_of_field.hasOwnProperty(value_based_on_cc_change)) {
			$create_element = createElementBasedOnType(type_of_field[value_based_on_cc_change], rule_name, data_sel_attr, main_tr_id, selectOptionData, value_based_on_cc_change);
		}
		where_append_position.append($create_element);
		if ('select' == type_of_field[value_based_on_cc_change]) {
			commonMultiFilter('.asw_' + rule_name + '_' + data_sel_attr + '_condition_value_cls', 'aws_get_value_based_on_' + data_sel_attr);
			if ('country' === data_sel_attr) {
				jQuery('.asw_' + rule_name + '_' + data_sel_attr + '_condition_value_cls').select2();
			}
		}
	}
	
	var AWS_ADMIN = {
		init: function () {
			/*based on common for all filter*/
			checkAllFilter();
			removeMinusButton();
			checkCHKcheckedThenLoadTR();
			jQuery(document).on('click', '.add-aws-condition', AWS_ADMIN.addAWSCondition);
			jQuery(document).on('click', '.remove-aws-condition', AWS_ADMIN.removeAWSCondition);
			/*Cart contains onchange*/
			jQuery(document).on('change', '.asw_condition_nt_rmd_data', AWS_ADMIN.changeConditionValue);
			/*Checkbox Checked*/
			jQuery(document).on('change', '.asw_rule_chk_status', AWS_ADMIN.showRuleSection);
			/*APU Checkbox Checked*/
			jQuery(document).on('change', '.asw_apu_nt_rmd_data', AWS_ADMIN.apuChecked);
			/*On change unit*/
			jQuery(document).on('change', '.asw_ps_ut_select', AWS_ADMIN.changePerUnit);
		},
		changePerUnit: function () {
			var main_tr_id = $(this).parent().parent().attr('id');
			var current_unit_val = $(this).val();
			var rule_name = $(this).parent().parent().parent().parent().parent().parent().attr('data-gb');
			
			if (jQuery('#' + 'asw_' + rule_name + '_apsubunit_id_' + main_tr_id).length) {
				jQuery('#' + 'asw_' + rule_name + '_apsubunit_id_' + main_tr_id).remove();
			}
			var after_appnd_td = jQuery('#' + 'asw_' + rule_name + '_apsub_id_' + main_tr_id + ' input');
			var $td_label_apsub_cpu = document.createElement('label');
			$td_label_apsub_cpu.setAttribute('id', 'asw_' + rule_name + '_apsubunit_id_' + main_tr_id);
			var $td_label_apsub_txtnode_cpu = '';
			if ('st_without_tax_disc' === current_unit_val) {
				$td_label_apsub_txtnode_cpu = document.createTextNode(aws_var.currency_symbol);
			}
			if ('weight' === current_unit_val) {
				$td_label_apsub_txtnode_cpu = document.createTextNode(aws_var.weight_unit);
			}
			$td_label_apsub_cpu.appendChild($td_label_apsub_txtnode_cpu);
			after_appnd_td.after($td_label_apsub_cpu);
		},
		apuChecked: function () {
			var chk_id = $(this).attr('id');
			var rule_name = $(this).parent().parent().parent().parent().parent().parent().attr('data-gb');
			var main_tr_id = $(this).parent().parent().attr('id');
			if ($('input[id=' + chk_id + ']').is(':checked')) {
				jQuery('#' + 'asw_' + rule_name + '_minval_id_' + main_tr_id).remove();
				jQuery('#' + 'asw_' + rule_name + '_maxval_id_' + main_tr_id).remove();
				
				/*Start TD for ps condition apply per unit number*/
				var first_appned = jQuery('#' + 'asw_' + rule_name + '_apu_id_' + main_tr_id);
				
				var $td_apsub = document.createElement('td');
				$td_apsub.setAttribute('id', 'asw_' + rule_name + '_apsub_id_0');
				$td_apsub.setAttribute('class', 'asw_' + rule_name + '_apsub_div asw_sub_div');
				
				var $td_label_apsub = document.createElement('label');
				$td_label_apsub.setAttribute('for', 'asw_' + rule_name + '_apsub_id_0');
				var $td_label_apsub_txtnode = document.createTextNode('Each');
				$td_label_apsub.appendChild($td_label_apsub_txtnode);
				$td_apsub.appendChild($td_label_apsub);
				
				var $td_apsub_elem = document.createElement('input');
				$td_apsub_elem.setAttribute('type', 'number');
				$td_apsub_elem.setAttribute('id', 'asw_' + rule_name + '_apsub_select_id_0');
				$td_apsub_elem.setAttribute('class', 'asw_' + rule_name + '_apsub_nt_rmd_data asw_apsub_nt_rmd_data');
				$td_apsub_elem.setAttribute('name', 'asw_condition_name[' + rule_name + '][0][advanced][apsub]');
				$td_apsub_elem.setAttribute('placeholder', 'Each');
				$td_apsub_elem.setAttribute('value', '1');
				$td_apsub_elem.setAttribute('min', '1');
				$td_apsub.appendChild($td_apsub_elem);
				
				first_appned.after($td_apsub);
				/*End TD for ps condition apply per unit number*/
			} else {
				jQuery('#' + 'asw_' + rule_name + '_apsub_id_' + main_tr_id).remove();
				var ut_append = jQuery('#' + 'asw_' + rule_name + '_ut_id_' + main_tr_id);
				/*Start TD for ps condition min value*/
				var $td_minval = document.createElement('td');
				$td_minval.setAttribute('id', 'asw_' + rule_name + '_minval_id_' + main_tr_id);
				$td_minval.setAttribute('class', 'asw_' + rule_name + '_minval_div asw_sub_div');
				
				var $td_minval_elem = document.createElement('input');
				$td_minval_elem.setAttribute('type', 'textbox');
				$td_minval_elem.setAttribute('id', 'asw_' + rule_name + '_minval_select_id_' + main_tr_id);
				$td_minval_elem.setAttribute('class', 'asw_' + rule_name + '_minval_nt_rmd_data asw_minval_nt_rmd_data');
				$td_minval_elem.setAttribute('name', 'asw_condition_name[' + rule_name + '][' + main_tr_id + '][advanced][minval]');
				$td_minval_elem.setAttribute('placeholder', 'Min Value');
				$td_minval_elem.setAttribute('value', '');
				$td_minval.appendChild($td_minval_elem);
				
				ut_append.after($td_minval);
				/*End TD for ps condition min value*/
				
				var second_appned = jQuery('#' + 'asw_' + rule_name + '_price_id_' + main_tr_id);
				/*Start TD for ps condition max value*/
				var $td_maxval = document.createElement('td');
				$td_maxval.setAttribute('id', 'asw_' + rule_name + '_maxval_id_' + main_tr_id);
				$td_maxval.setAttribute('class', 'asw_' + rule_name + '_maxval_div asw_sub_div');
				
				var $td_maxval_elem = document.createElement('input');
				$td_maxval_elem.setAttribute('type', 'textbox');
				$td_maxval_elem.setAttribute('id', 'asw_' + rule_name + '_maxval_select_id_' + main_tr_id);
				$td_maxval_elem.setAttribute('class', 'asw_' + rule_name + '_maxval_nt_rmd_data asw_maxval_nt_rmd_data');
				$td_maxval_elem.setAttribute('name', 'asw_condition_name[' + rule_name + '][' + main_tr_id + '][advanced][maxval]');
				$td_maxval_elem.setAttribute('placeholder', 'Max Value');
				$td_maxval_elem.setAttribute('value', '');
				$td_maxval.appendChild($td_maxval_elem);
				
				second_appned.before($td_maxval);
				/*End TD for ps condition max value*/
			}
		},
		showRuleSection: function () {
			var chk_name = $(this).attr('id');
			var rule_name = $(this).attr('data-gb');
			var new_tr_inside_table_length = jQuery('tr[data-gb=' + rule_name + ']').length;
			var total_tr_id = jQuery('tr[data-gb=' + rule_name + '] .asw-extra-rule-div tr').length;
			
			if ($('input[id=' + chk_name + ']').is(':checked')) {
				$(this).parent().parent().next().show();
				if (new_tr_inside_table_length == 0) {
					addNewTRIfBlank($(this), rule_name, total_tr_id, new_tr_inside_table_length);
				}
			} else {
				$(this).parent().parent().next().hide();
			}
		},
		addAWSCondition: function () {
			var rule_name = $(this).parent().parent().parent().parent().parent().parent().attr('data-gb');
			$('.multiselect2').select2('destroy');
			var $tr = $(this).closest('.tr_clone');
			var selectedConditionValue = $(this).parent().parent().find('.asw_' + rule_name + '_condition_name_div select').val();
			var new_tr_id = parseInt(jQuery('tr[data-gb=' + rule_name + '] .asw-extra-rule-div tr').length) + 1;
			var $clone = $tr.clone().prop('id', new_tr_id);
			$tr.after($clone);
			
			$clone.find('.asw_' + rule_name + '_condition_name_div').attr('id', 'asw_' + rule_name + '_condition_name_div_' + new_tr_id);
			$clone.find('.asw_' + rule_name + '_condition_name_div select').attr('class', 'asw_' + rule_name + '_condition_nt_rmd_data asw_condition_nt_rmd_data');
			$clone.find('.asw_' + rule_name + '_condition_name_div select').val(selectedConditionValue);
			
			$clone.find('.asw_' + rule_name + '_condition_name_div .asw_' + rule_name + '_condition_nt_rmd_data').attr('name', 'asw_condition_name[' + rule_name + '][' + new_tr_id + '][condition]');
			$clone.find('.asw_' + rule_name + '_condition_name_div .asw_' + rule_name + '_condition_nt_rmd_data').attr('id', 'asw_' + rule_name + '_condition_select_id_' + new_tr_id);
			
			if ('ps' != rule_name) {
				$clone.find('.asw_' + rule_name + '_operator_div').attr('id', 'asw_' + rule_name + '_operator_id_' + new_tr_id);
				$clone.find('.asw_' + rule_name + '_operator_div select').attr('class', 'asw_' + rule_name + '_operator_nt_rmd_data asw_operator_nt_rmd_data');
				$clone.find('.asw_' + rule_name + '_operator_div .asw_' + rule_name + '_operator_nt_rmd_data').attr('name', 'asw_condition_name[' + rule_name + '][' + new_tr_id + '][operator]');
				$clone.find('.asw_' + rule_name + '_operator_div .asw_' + rule_name + '_operator_nt_rmd_data').attr('id', 'asw_' + rule_name + '_operator_select_id_' + new_tr_id);
			}
			
			$clone.find('.asw_' + rule_name + '_condition_value_div').attr('id', 'asw_' + rule_name + '_condition_value_id_' + new_tr_id);
			
			if ('ps' == rule_name) {
				$clone.find('.asw_' + rule_name + '_condition_value_div label').attr('for', 'asw_' + rule_name + '_condition_value_label_id_' + new_tr_id);
				$clone.find('.asw_' + rule_name + '_condition_value_div label').attr('id', 'asw_' + rule_name + '_condition_value_label_id_' + new_tr_id);
				$clone.find('.asw_' + rule_name + '_condition_value_div input').attr('id', 'asw_' + rule_name + '_condition_value_select_id_' + new_tr_id);
				$clone.find('.asw_' + rule_name + '_condition_value_div input').attr('name', 'asw_condition_name[' + rule_name + '][' + new_tr_id + '][value]');
				
				$clone.find('.asw_' + rule_name + '_apu_div').attr('id', 'asw_' + rule_name + '_apu_id_' + new_tr_id);
				$clone.find('.asw_' + rule_name + '_apu_div input').attr('id', 'asw_' + rule_name + '_apu_select_id_' + new_tr_id);
				$clone.find('.asw_' + rule_name + '_apu_div input').attr('name', 'asw_condition_name[' + rule_name + '][' + new_tr_id + '][advanced][apu]');
				$clone.find('.asw_' + rule_name + '_apu_div label').attr('for', 'asw_' + rule_name + '_apu_select_id_' + new_tr_id);
				
				$clone.find('.asw_' + rule_name + '_apsub_div').attr('id', 'asw_' + rule_name + '_apsub_id_' + new_tr_id);
				$clone.find('.asw_' + rule_name + '_apsub_div input').attr('id', 'asw_' + rule_name + '_apsub_select_id_' + new_tr_id);
				$clone.find('.asw_' + rule_name + '_apsub_div input').attr('name', 'asw_condition_name[' + rule_name + '][' + new_tr_id + '][advanced][apsub]');
				
				$clone.find('.asw_' + rule_name + '_ut_div').attr('id', 'asw_' + rule_name + '_ut_id_' + new_tr_id);
				$clone.find('.asw_' + rule_name + '_ut_div select').attr('name', 'asw_condition_name[' + rule_name + '][' + new_tr_id + '][advanced][ut]');
				
				$clone.find('.asw_' + rule_name + '_minval_div').attr('id', 'asw_' + rule_name + '_minval_id_' + new_tr_id);
				$clone.find('.asw_' + rule_name + '_minval_div input').attr('id', 'asw_' + rule_name + '_minval_select_id_' + new_tr_id);
				$clone.find('.asw_' + rule_name + '_minval_div input').attr('name', 'asw_condition_name[' + rule_name + '][' + new_tr_id + '][advanced][minval]');
				
				$clone.find('.asw_' + rule_name + '_maxval_div').attr('id', 'asw_' + rule_name + '_maxval_id_' + new_tr_id);
				$clone.find('.asw_' + rule_name + '_maxval_div input').attr('id', 'asw_' + rule_name + '_maxval_select_id_' + new_tr_id);
				$clone.find('.asw_' + rule_name + '_maxval_div input').attr('name', 'asw_condition_name[' + rule_name + '][' + new_tr_id + '][advanced][maxval]');
				
				$clone.find('.asw_' + rule_name + '_price_div').attr('id', 'asw_' + rule_name + '_price_id_' + new_tr_id);
				$clone.find('.asw_' + rule_name + '_price_div input').attr('id', 'asw_' + rule_name + '_price_select_id_' + new_tr_id);
				$clone.find('.asw_' + rule_name + '_price_div input').attr('name', 'asw_condition_name[' + rule_name + '][' + new_tr_id + '][advanced][price]');
			}
			
			var check_condition_value = $clone.find('.asw_' + rule_name + '_condition_name_div .asw_' + rule_name + '_condition_nt_rmd_data').val();
			if ('cc_subtotal_af_disc' === check_condition_value || 'cc_country' === check_condition_value || 'cc_products' === check_condition_value) {
				var data_sel_attr = getOtDynamicValue(check_condition_value);
				var type_of_field = JSON.parse(aws_var.type_of_field);
				if ('select' == type_of_field[check_condition_value]) {
					$clone.find('.asw_sub_div .multiselect2').attr('name', 'asw_condition_name[' + rule_name + '][' + new_tr_id + '][value][]');
					$clone.find('.asw_sub_div .multiselect2').attr('id', 'asw_' + rule_name + '_condition_value_select_id_' + new_tr_id);
					$clone.find('.asw_sub_div .multiselect2').attr('class', 'asw_' + rule_name + '_' + data_sel_attr + '_condition_value_cls multiselect2');
				} else {
					$clone.find('.asw_sub_div .asw-td-input-field').attr('name', 'asw_condition_name[' + rule_name + '][' + new_tr_id + '][value]');
					$clone.find('.asw_sub_div .asw-td-input-field').attr('id', 'asw_' + rule_name + '_condition_value_select_id_' + new_tr_id);
				}
				if ($clone.find('.asw_condition_remove').length == 0) {
					var create_remove_td = document.createElement('td');
					create_remove_td.setAttribute('class', 'asw_condition_remove');
					create_remove_td.setAttribute('id', 'asw_' + rule_name + '_condition_remove_id');
					
					var create_remove_td_a = document.createElement('a');
					create_remove_td_a.setAttribute('class', 'button remove-aws-condition');
					create_remove_td_a.setAttribute('data-group', '0');
					create_remove_td_a.setAttribute('href', 'javascript:void(0);');
					
					var create_remove_td_a_textnode = document.createTextNode('-');
					create_remove_td_a.appendChild(create_remove_td_a_textnode);
					create_remove_td.appendChild(create_remove_td_a);
					
					$clone.find('.asw_condition_add').after(create_remove_td);
				}
				
				checkAllFilter();
				
				if ('select' == type_of_field[check_condition_value]) {
					$clone.find('#asw_' + rule_name + '_condition_value_select_id_' + new_tr_id).select2('val', '');
				}
			}
		},
		removeAWSCondition: function () {
			var rule_name = $(this).parent().parent().parent().parent().parent().parent().attr('data-gb');
			jQuery(this).parent().parent().remove();
			removeMinusButton();
			jQuery('tr[data-gb=' + rule_name + '] .asw-extra-rule-div tr').each(function (index, element) {
				jQuery(this).attr('id', index);
				
				jQuery(this).find('.asw_' + rule_name + '_condition_name_div').attr('id', 'asw_condition_name_div_' + index);
				jQuery(this).find('.asw_' + rule_name + '_condition_name_div .asw_' + rule_name + '_condition_nt_rmd_data').attr('name', 'asw_condition_name[' + rule_name + '][' + index + '][condition]');
				jQuery(this).find('.asw_' + rule_name + '_condition_name_div .asw_' + rule_name + '_condition_nt_rmd_data').attr('id', 'asw_cart_condition_select_id_' + index);
				
				jQuery(this).find('.asw_' + rule_name + '_operator_div').attr('id', 'asw_operator_id_' + index);
				jQuery(this).find('.asw_' + rule_name + '_operator_div .asw_' + rule_name + '_condition_nt_rmd_data').attr('name', 'asw_condition_name[' + rule_name + '][' + index + '][operator]');
				jQuery(this).find('.asw_' + rule_name + '_operator_div .asw_' + rule_name + '_condition_nt_rmd_data').attr('id', 'asw_cart_operator_select_id_' + index);
				
				jQuery(this).find('.asw_' + rule_name + '_condition_value_div').attr('id', 'asw_condition_value_id_' + index);
				
				jQuery(this).find('.asw_sub_div .multiselect2').attr('name', 'asw_condition_name[' + rule_name + '][' + index + '][value][]');
				jQuery(this).find('.asw_sub_div .multiselect2').attr('id', 'asw_' + rule_name + '_condition_value_select_id_' + index);
			});
		},
		changeConditionValue: function () {
			var main_tr_id = $(this).parent().parent().attr('id');
			var rule_name = $(this).parent().parent().parent().parent().parent().parent().attr('data-gb');
			var value_based_on_cc_change = $(this).val();
			if ('cc_subtotal_af_disc' === value_based_on_cc_change || 'cc_country' === value_based_on_cc_change || 'cc_products' === value_based_on_cc_change) {
				var data_sel_attr = getOtDynamicValue(value_based_on_cc_change);
				var remove_old_select;
				var where_append_position;
				
				if ('ps' !== rule_name) {
					remove_old_select = $(this).parent().next().next().children();
					remove_old_select.remove();
					where_append_position = $(this).parent().next().next();
					var $second_td_select = $(this).parent().next().find('.asw_' + rule_name + '_operator_nt_rmd_data');
					var $get_second_td_select = $second_td_select.attr('id');
					var $second_td_select_by_id = document.getElementById($get_second_td_select);
					ChangeOperatorData($get_second_td_select, $second_td_select_by_id, value_based_on_cc_change, 'onchange');
				}
				
				if ('ps' === rule_name) {
					remove_old_select = $(this).parent().next().children();
					remove_old_select.remove();
					where_append_position = $(this).parent().next();
					
					var getSelectId = jQuery('#' + 'asw_' + rule_name + '_ut_id_' + main_tr_id + ' select option[value="qty"]');
					if ('cc_tc_spec' !== value_based_on_cc_change) {
						var $per_unit_cart_option = JSON.parse(aws_var.per_unit_cart_option);
						if ($per_unit_cart_option) {
							for (var per_unit_cart in $per_unit_cart_option) {
								$('#asw_' + rule_name + '_ut_id_' + main_tr_id + ' option[value=' + per_unit_cart + ']').remove();
							}
						}
						var $per_unit_item_option = JSON.parse(aws_var.per_unit_item_option);
						if ($per_unit_item_option) {
							for (var per_unit_item in $per_unit_item_option) {
								var $option = document.createElement('option');
								$option.setAttribute('value', per_unit_item);
								var $option_texnode = document.createTextNode($per_unit_item_option[per_unit_item]);
								$option.appendChild($option_texnode);
								getSelectId.after($option);
							}
						}
					} else {
						var $per_unit_item_option = JSON.parse(aws_var.per_unit_item_option);
						if ($per_unit_item_option) {
							for (var per_unit_item in $per_unit_item_option) {
								$('#asw_' + rule_name + '_ut_id_' + main_tr_id + ' option[value=' + per_unit_item + ']').remove();
							}
						}
						
						var $per_unit_cart_option = JSON.parse(aws_var.per_unit_cart_option);
						if ($per_unit_cart_option) {
							for (var per_unit_cart in $per_unit_cart_option) {
								var $option = document.createElement('option');
								$option.setAttribute('value', per_unit_cart);
								var $option_texnode = document.createTextNode($per_unit_cart_option[per_unit_cart]);
								$option.appendChild($option_texnode);
								getSelectId.after($option);
							}
						}
					}
				}
				
				var selectOptionData;
				if ('cb' === rule_name) {
					selectOptionData = '';
				} else if ('lb' === rule_name) {
					if ('country' === data_sel_attr) {
						selectOptionData = JSON.parse(aws_var.country_obj);
					} else {
						selectOptionData = '';
					}
				}  else if ('ps' === rule_name) {
					if ('shpc_spec' === data_sel_attr) {
						selectOptionData = JSON.parse(aws_var.shipping_class_obj);
					} else {
						selectOptionData = '';
					}
				}
				aswarrayOfFieldType(value_based_on_cc_change, rule_name, data_sel_attr, main_tr_id, where_append_position, selectOptionData);
			}
		}
	};
	
	$(function () {
		AWS_ADMIN.init();
		$('.tips, .help_tip, .woocommerce-help-tip').tipTip({
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200
		});
	});
} )(jQuery);