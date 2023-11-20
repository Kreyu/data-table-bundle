<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* @KreyuDataTable/themes/base.html.twig */
class __TwigTemplate_bf8b8ff352f073197cf10407d5a0c1f1 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'kreyu_data_table' => [$this, 'block_kreyu_data_table'],
            'kreyu_data_table_form_aware' => [$this, 'block_kreyu_data_table_form_aware'],
            'kreyu_data_table_table' => [$this, 'block_kreyu_data_table_table'],
            'kreyu_data_table_action_bar' => [$this, 'block_kreyu_data_table_action_bar'],
            'action_bar' => [$this, 'block_action_bar'],
            'table' => [$this, 'block_table'],
            'table_head' => [$this, 'block_table_head'],
            'table_head_row' => [$this, 'block_table_head_row'],
            'table_body' => [$this, 'block_table_body'],
            'table_body_row' => [$this, 'block_table_body_row'],
            'table_body_row_results' => [$this, 'block_table_body_row_results'],
            'table_body_row_no_results' => [$this, 'block_table_body_row_no_results'],
            'pagination' => [$this, 'block_pagination'],
            'kreyu_data_table_header_row' => [$this, 'block_kreyu_data_table_header_row'],
            'kreyu_data_table_value_row' => [$this, 'block_kreyu_data_table_value_row'],
            'kreyu_data_table_column_label' => [$this, 'block_kreyu_data_table_column_label'],
            'kreyu_data_table_column_header' => [$this, 'block_kreyu_data_table_column_header'],
            'kreyu_data_table_column_value' => [$this, 'block_kreyu_data_table_column_value'],
            'kreyu_data_table_action' => [$this, 'block_kreyu_data_table_action'],
            'kreyu_data_table_pagination' => [$this, 'block_kreyu_data_table_pagination'],
            'pagination_widget' => [$this, 'block_pagination_widget'],
            'pagination_controls' => [$this, 'block_pagination_controls'],
            'pagination_counters' => [$this, 'block_pagination_counters'],
            'pagination_counters_message' => [$this, 'block_pagination_counters_message'],
            'pagination_page' => [$this, 'block_pagination_page'],
            'pagination_page_active' => [$this, 'block_pagination_page_active'],
            'pagination_page_message' => [$this, 'block_pagination_page_message'],
            'pagination_first' => [$this, 'block_pagination_first'],
            'pagination_first_disabled' => [$this, 'block_pagination_first_disabled'],
            'pagination_first_message' => [$this, 'block_pagination_first_message'],
            'pagination_previous' => [$this, 'block_pagination_previous'],
            'pagination_previous_disabled' => [$this, 'block_pagination_previous_disabled'],
            'pagination_previous_message' => [$this, 'block_pagination_previous_message'],
            'pagination_last' => [$this, 'block_pagination_last'],
            'pagination_last_disabled' => [$this, 'block_pagination_last_disabled'],
            'pagination_last_message' => [$this, 'block_pagination_last_message'],
            'pagination_next' => [$this, 'block_pagination_next'],
            'pagination_next_disabled' => [$this, 'block_pagination_next_disabled'],
            'pagination_next_message' => [$this, 'block_pagination_next_message'],
            'kreyu_data_table_filters_form' => [$this, 'block_kreyu_data_table_filters_form'],
            'filtration_widget' => [$this, 'block_filtration_widget'],
            'filtration_form' => [$this, 'block_filtration_form'],
            'filtration_form_content' => [$this, 'block_filtration_form_content'],
            'filtration_form_row' => [$this, 'block_filtration_form_row'],
            'filtration_form_submit' => [$this, 'block_filtration_form_submit'],
            'kreyu_data_table_date_range_widget' => [$this, 'block_kreyu_data_table_date_range_widget'],
            'column_header' => [$this, 'block_column_header'],
            'column_header_label' => [$this, 'block_column_header_label'],
            'column_value' => [$this, 'block_column_value'],
            'column_text_value' => [$this, 'block_column_text_value'],
            'column_number_value' => [$this, 'block_column_number_value'],
            'column_money_value' => [$this, 'block_column_money_value'],
            'column_link_value' => [$this, 'block_column_link_value'],
            'column_date_time_value' => [$this, 'block_column_date_time_value'],
            'column_date_period_value' => [$this, 'block_column_date_period_value'],
            'column_boolean_value' => [$this, 'block_column_boolean_value'],
            'column_collection_value' => [$this, 'block_column_collection_value'],
            'column_collection_separator' => [$this, 'block_column_collection_separator'],
            'column_template_value' => [$this, 'block_column_template_value'],
            'column_actions_value' => [$this, 'block_column_actions_value'],
            'column_form_value' => [$this, 'block_column_form_value'],
            'column_checkbox_header' => [$this, 'block_column_checkbox_header'],
            'column_checkbox_value' => [$this, 'block_column_checkbox_value'],
            'action_value_icon' => [$this, 'block_action_value_icon'],
            'action_value' => [$this, 'block_action_value'],
            'action_link_value' => [$this, 'block_action_link_value'],
            'action_button_value' => [$this, 'block_action_button_value'],
            'action_form_value' => [$this, 'block_action_form_value'],
            'sort_arrow_none' => [$this, 'block_sort_arrow_none'],
            'sort_arrow_asc' => [$this, 'block_sort_arrow_asc'],
            'sort_arrow_desc' => [$this, 'block_sort_arrow_desc'],
            'attributes' => [$this, 'block_attributes'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@KreyuDataTable/themes/base.html.twig"));

        // line 2
        echo "
";
        // line 4
        echo "
";
        // line 5
        $this->displayBlock('kreyu_data_table', $context, $blocks);
        // line 19
        echo "
";
        // line 20
        $this->displayBlock('kreyu_data_table_form_aware', $context, $blocks);
        // line 33
        echo "
";
        // line 34
        $this->displayBlock('kreyu_data_table_table', $context, $blocks);
        // line 37
        echo "
";
        // line 38
        $this->displayBlock('kreyu_data_table_action_bar', $context, $blocks);
        // line 41
        echo "
";
        // line 42
        $this->displayBlock('action_bar', $context, $blocks);
        // line 43
        echo "
";
        // line 44
        $this->displayBlock('table', $context, $blocks);
        // line 50
        echo "
";
        // line 51
        $this->displayBlock('table_head', $context, $blocks);
        // line 54
        echo "
";
        // line 55
        $this->displayBlock('table_head_row', $context, $blocks);
        // line 58
        echo "
";
        // line 59
        $this->displayBlock('table_body', $context, $blocks);
        // line 62
        echo "
";
        // line 63
        $this->displayBlock('table_body_row', $context, $blocks);
        // line 70
        echo "
";
        // line 71
        $this->displayBlock('table_body_row_results', $context, $blocks);
        // line 76
        echo "
";
        // line 77
        $this->displayBlock('table_body_row_no_results', $context, $blocks);
        // line 82
        echo "
";
        // line 83
        $this->displayBlock('pagination', $context, $blocks);
        // line 86
        echo "
";
        // line 87
        $this->displayBlock('kreyu_data_table_header_row', $context, $blocks);
        // line 94
        echo "
";
        // line 95
        $this->displayBlock('kreyu_data_table_value_row', $context, $blocks);
        // line 102
        echo "
";
        // line 103
        $this->displayBlock('kreyu_data_table_column_label', $context, $blocks);
        // line 110
        echo "
";
        // line 111
        $this->displayBlock('kreyu_data_table_column_header', $context, $blocks);
        // line 114
        echo "
";
        // line 115
        $this->displayBlock('kreyu_data_table_column_value', $context, $blocks);
        // line 118
        echo "
";
        // line 119
        $this->displayBlock('kreyu_data_table_action', $context, $blocks);
        // line 122
        echo "
";
        // line 124
        echo "
";
        // line 125
        $this->displayBlock('kreyu_data_table_pagination', $context, $blocks);
        // line 130
        echo "
";
        // line 131
        $this->displayBlock('pagination_widget', $context, $blocks);
        // line 135
        echo "
";
        // line 136
        $this->displayBlock('pagination_controls', $context, $blocks);
        // line 173
        echo "
";
        // line 174
        $this->displayBlock('pagination_counters', $context, $blocks);
        // line 179
        echo "
";
        // line 180
        $this->displayBlock('pagination_counters_message', $context, $blocks);
        // line 187
        echo "
";
        // line 188
        $this->displayBlock('pagination_page', $context, $blocks);
        // line 193
        echo "
";
        // line 194
        $this->displayBlock('pagination_page_active', $context, $blocks);
        // line 199
        echo "
";
        // line 200
        $this->displayBlock('pagination_page_message', $context, $blocks);
        // line 201
        echo "
";
        // line 202
        $this->displayBlock('pagination_first', $context, $blocks);
        // line 207
        echo "
";
        // line 208
        $this->displayBlock('pagination_first_disabled', $context, $blocks);
        // line 213
        echo "
";
        // line 214
        $this->displayBlock('pagination_first_message', $context, $blocks);
        // line 215
        echo "
";
        // line 216
        $this->displayBlock('pagination_previous', $context, $blocks);
        // line 221
        echo "
";
        // line 222
        $this->displayBlock('pagination_previous_disabled', $context, $blocks);
        // line 225
        echo "
";
        // line 226
        $this->displayBlock('pagination_previous_message', $context, $blocks);
        // line 227
        echo "
";
        // line 228
        $this->displayBlock('pagination_last', $context, $blocks);
        // line 233
        echo "
";
        // line 234
        $this->displayBlock('pagination_last_disabled', $context, $blocks);
        // line 237
        echo "
";
        // line 238
        $this->displayBlock('pagination_last_message', $context, $blocks);
        // line 239
        echo "
";
        // line 240
        $this->displayBlock('pagination_next', $context, $blocks);
        // line 245
        echo "
";
        // line 246
        $this->displayBlock('pagination_next_disabled', $context, $blocks);
        // line 249
        echo "
";
        // line 250
        $this->displayBlock('pagination_next_message', $context, $blocks);
        // line 251
        echo "
";
        // line 253
        echo "
";
        // line 254
        $this->displayBlock('kreyu_data_table_filters_form', $context, $blocks);
        // line 265
        echo "
";
        // line 266
        $this->displayBlock('filtration_widget', $context, $blocks);
        // line 269
        echo "
";
        // line 270
        $this->displayBlock('filtration_form', $context, $blocks);
        // line 273
        echo "
";
        // line 274
        $this->displayBlock('filtration_form_content', $context, $blocks);
        // line 283
        echo "
";
        // line 284
        $this->displayBlock('filtration_form_row', $context, $blocks);
        // line 287
        echo "
";
        // line 288
        $this->displayBlock('filtration_form_submit', $context, $blocks);
        // line 293
        echo "
";
        // line 294
        $this->displayBlock('kreyu_data_table_date_range_widget', $context, $blocks);
        // line 298
        echo "
";
        // line 300
        echo "
";
        // line 301
        $this->displayBlock('column_header', $context, $blocks);
        // line 347
        echo "
";
        // line 348
        $this->displayBlock('column_header_label', $context, $blocks);
        // line 355
        echo "
";
        // line 357
        echo "
";
        // line 358
        $this->displayBlock('column_value', $context, $blocks);
        // line 367
        echo "
";
        // line 368
        $this->displayBlock('column_text_value', $context, $blocks);
        // line 371
        echo "
";
        // line 372
        $this->displayBlock('column_number_value', $context, $blocks);
        // line 375
        echo "
";
        // line 376
        $this->displayBlock('column_money_value', $context, $blocks);
        // line 379
        echo "
";
        // line 380
        $this->displayBlock('column_link_value', $context, $blocks);
        // line 385
        echo "
";
        // line 386
        $this->displayBlock('column_date_time_value', $context, $blocks);
        // line 391
        echo "
";
        // line 392
        $this->displayBlock('column_date_period_value', $context, $blocks);
        // line 397
        echo "
";
        // line 398
        $this->displayBlock('column_boolean_value', $context, $blocks);
        // line 403
        echo "
";
        // line 404
        $this->displayBlock('column_collection_value', $context, $blocks);
        // line 412
        echo "
";
        // line 413
        $this->displayBlock('column_collection_separator', $context, $blocks);
        // line 416
        echo "
";
        // line 417
        $this->displayBlock('column_template_value', $context, $blocks);
        // line 420
        echo "
";
        // line 421
        $this->displayBlock('column_actions_value', $context, $blocks);
        // line 426
        echo "
";
        // line 427
        $this->displayBlock('column_form_value', $context, $blocks);
        // line 442
        echo "
";
        // line 443
        $this->displayBlock('column_checkbox_header', $context, $blocks);
        // line 456
        echo "
";
        // line 457
        $this->displayBlock('column_checkbox_value', $context, $blocks);
        // line 470
        echo "
";
        // line 472
        echo "
";
        // line 473
        $this->displayBlock('action_value_icon', $context, $blocks);
        // line 474
        echo "
";
        // line 475
        $this->displayBlock('action_value', $context, $blocks);
        // line 479
        echo "
";
        // line 480
        $this->displayBlock('action_link_value', $context, $blocks);
        // line 485
        echo "
";
        // line 486
        $this->displayBlock('action_button_value', $context, $blocks);
        // line 497
        echo "
";
        // line 498
        $this->displayBlock('action_form_value', $context, $blocks);
        // line 515
        echo "
";
        // line 516
        $this->displayBlock('sort_arrow_none', $context, $blocks);
        // line 517
        echo "
";
        // line 518
        $this->displayBlock('sort_arrow_asc', $context, $blocks);
        // line 519
        echo "
";
        // line 520
        $this->displayBlock('sort_arrow_desc', $context, $blocks);
        // line 522
        $this->displayBlock('attributes', $context, $blocks);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 5
    public function block_kreyu_data_table($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "kreyu_data_table"));

        // line 6
        echo "    <turbo-frame id=\"kreyu_data_table_";
        echo twig_escape_filter($this->env, (isset($context["name"]) || array_key_exists("name", $context) ? $context["name"] : (function () { throw new RuntimeError('Variable "name" does not exist.', 6, $this->source); })()), "html", null, true);
        echo "\"
        ";
        // line 7
        if ((isset($context["has_batch_actions"]) || array_key_exists("has_batch_actions", $context) ? $context["has_batch_actions"] : (function () { throw new RuntimeError('Variable "has_batch_actions" does not exist.', 7, $this->source); })())) {
            // line 8
            echo "            data-controller=\"kreyu--data-table-bundle--batch\"
        ";
        }
        // line 10
        echo "    >
        ";
        // line 11
        $this->displayBlock("action_bar", $context, $blocks);
        echo "
        ";
        // line 12
        $this->displayBlock("table", $context, $blocks);
        echo "

        ";
        // line 14
        if ((isset($context["pagination_enabled"]) || array_key_exists("pagination_enabled", $context) ? $context["pagination_enabled"] : (function () { throw new RuntimeError('Variable "pagination_enabled" does not exist.', 14, $this->source); })())) {
            // line 15
            echo "            ";
            echo $this->extensions['Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension']->renderPagination($this->env, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 15, $this->source); })()));
            echo "
        ";
        }
        // line 17
        echo "    </turbo-frame>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 20
    public function block_kreyu_data_table_form_aware($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "kreyu_data_table_form_aware"));

        // line 21
        echo "    <turbo-frame id=\"kreyu_data_table_";
        echo twig_escape_filter($this->env, (isset($context["name"]) || array_key_exists("name", $context) ? $context["name"] : (function () { throw new RuntimeError('Variable "name" does not exist.', 21, $this->source); })()), "html", null, true);
        echo "\">
        ";
        // line 22
        $this->displayBlock("action_bar", $context, $blocks);
        echo "

        ";
        // line 24
        echo         $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderBlock((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 24, $this->source); })()), 'form_start', (isset($context["form_variables"]) || array_key_exists("form_variables", $context) ? $context["form_variables"] : (function () { throw new RuntimeError('Variable "form_variables" does not exist.', 24, $this->source); })()));
        echo "
            ";
        // line 25
        $this->displayBlock("table", $context, $blocks);
        echo "
        ";
        // line 26
        echo         $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderBlock((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 26, $this->source); })()), 'form_end', ["render_rest" => false]);
        echo "

        ";
        // line 28
        if ((isset($context["pagination_enabled"]) || array_key_exists("pagination_enabled", $context) ? $context["pagination_enabled"] : (function () { throw new RuntimeError('Variable "pagination_enabled" does not exist.', 28, $this->source); })())) {
            // line 29
            echo "            ";
            echo $this->extensions['Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension']->renderPagination($this->env, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 29, $this->source); })()));
            echo "
        ";
        }
        // line 31
        echo "    </turbo-frame>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 34
    public function block_kreyu_data_table_table($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "kreyu_data_table_table"));

        // line 35
        echo "    ";
        $this->displayBlock("table", $context, $blocks);
        echo "
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 38
    public function block_kreyu_data_table_action_bar($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "kreyu_data_table_action_bar"));

        // line 39
        echo "    ";
        $this->displayBlock("action_bar", $context, $blocks);
        echo "
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 42
    public function block_action_bar($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "action_bar"));

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 44
    public function block_table($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "table"));

        // line 45
        echo "    <table ";
        $__internal_compile_0 = $context;
        $__internal_compile_1 = ["attr" => ((array_key_exists("table_attr", $context)) ? (_twig_default_filter((isset($context["table_attr"]) || array_key_exists("table_attr", $context) ? $context["table_attr"] : (function () { throw new RuntimeError('Variable "table_attr" does not exist.', 45, $this->source); })()), [])) : ([]))];
        if (!twig_test_iterable($__internal_compile_1)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 45, $this->getSourceContext());
        }
        $__internal_compile_1 = twig_to_array($__internal_compile_1);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_1));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_0;
        echo ">
        ";
        // line 46
        $this->displayBlock("table_head", $context, $blocks);
        echo "
        ";
        // line 47
        $this->displayBlock("table_body", $context, $blocks);
        echo "
    </table>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 51
    public function block_table_head($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "table_head"));

        // line 52
        echo "    <thead>";
        $this->displayBlock("table_head_row", $context, $blocks);
        echo "</thead>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 55
    public function block_table_head_row($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "table_head_row"));

        // line 56
        echo "    ";
        echo $this->extensions['Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension']->renderHeaderRow($this->env, (isset($context["header_row"]) || array_key_exists("header_row", $context) ? $context["header_row"] : (function () { throw new RuntimeError('Variable "header_row" does not exist.', 56, $this->source); })()));
        echo "
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 59
    public function block_table_body($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "table_body"));

        // line 60
        echo "    <tbody>";
        $this->displayBlock("table_body_row", $context, $blocks);
        echo "</tbody>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 63
    public function block_table_body_row($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "table_body_row"));

        // line 64
        echo "    ";
        if ((twig_length_filter($this->env, (isset($context["value_rows"]) || array_key_exists("value_rows", $context) ? $context["value_rows"] : (function () { throw new RuntimeError('Variable "value_rows" does not exist.', 64, $this->source); })())) > 0)) {
            // line 65
            echo "        ";
            $this->displayBlock("table_body_row_results", $context, $blocks);
            echo "
    ";
        } else {
            // line 67
            echo "        ";
            $this->displayBlock("table_body_row_no_results", $context, $blocks);
            echo "
    ";
        }
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 71
    public function block_table_body_row_results($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "table_body_row_results"));

        // line 72
        echo "    ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["value_rows"]) || array_key_exists("value_rows", $context) ? $context["value_rows"] : (function () { throw new RuntimeError('Variable "value_rows" does not exist.', 72, $this->source); })()));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["value_row"]) {
            // line 73
            echo "        <tr ";
            $__internal_compile_2 = $context;
            $__internal_compile_3 = ["attr" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["value_row"], "vars", [], "any", false, false, false, 73), "attr", [], "any", false, false, false, 73)];
            if (!twig_test_iterable($__internal_compile_3)) {
                throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 73, $this->getSourceContext());
            }
            $__internal_compile_3 = twig_to_array($__internal_compile_3);
            $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_3));
            $this->displayBlock("attributes", $context, $blocks);
            $context = $__internal_compile_2;
            echo ">";
            echo $this->extensions['Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension']->renderValueRow($this->env, $context["value_row"]);
            echo "</tr>
    ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['value_row'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 77
    public function block_table_body_row_no_results($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "table_body_row_no_results"));

        // line 78
        echo "    <tr>
        <td colspan=\"";
        // line 79
        echo twig_escape_filter($this->env, (isset($context["column_count"]) || array_key_exists("column_count", $context) ? $context["column_count"] : (function () { throw new RuntimeError('Variable "column_count" does not exist.', 79, $this->source); })()), "html", null, true);
        echo "\" ";
        $__internal_compile_4 = $context;
        $__internal_compile_5 = ["attr" => ((array_key_exists("table_body_row_no_results_attr", $context)) ? (_twig_default_filter((isset($context["table_body_row_no_results_attr"]) || array_key_exists("table_body_row_no_results_attr", $context) ? $context["table_body_row_no_results_attr"] : (function () { throw new RuntimeError('Variable "table_body_row_no_results_attr" does not exist.', 79, $this->source); })()), [])) : ([]))];
        if (!twig_test_iterable($__internal_compile_5)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 79, $this->getSourceContext());
        }
        $__internal_compile_5 = twig_to_array($__internal_compile_5);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_5));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_4;
        echo ">";
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("No results", [], "KreyuDataTable"), "html", null, true);
        echo "</td>
    </tr>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 83
    public function block_pagination($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination"));

        // line 84
        echo "    ";
        echo $this->extensions['Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension']->renderPagination($this->env, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 84, $this->source); })()));
        echo "
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 87
    public function block_kreyu_data_table_header_row($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "kreyu_data_table_header_row"));

        // line 88
        echo "    <tr ";
        $__internal_compile_6 = $context;
        $__internal_compile_7 = ["attr" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["row"]) || array_key_exists("row", $context) ? $context["row"] : (function () { throw new RuntimeError('Variable "row" does not exist.', 88, $this->source); })()), "vars", [], "any", false, false, false, 88), "attr", [], "any", false, false, false, 88)];
        if (!twig_test_iterable($__internal_compile_7)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 88, $this->getSourceContext());
        }
        $__internal_compile_7 = twig_to_array($__internal_compile_7);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_7));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_6;
        echo ">
        ";
        // line 89
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["row"]) || array_key_exists("row", $context) ? $context["row"] : (function () { throw new RuntimeError('Variable "row" does not exist.', 89, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["column_header"]) {
            // line 90
            echo $this->extensions['Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension']->renderColumnHeader($this->env, $context["column_header"]);
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['column_header'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 92
        echo "    </tr>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 95
    public function block_kreyu_data_table_value_row($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "kreyu_data_table_value_row"));

        // line 96
        echo "    ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["row"]) || array_key_exists("row", $context) ? $context["row"] : (function () { throw new RuntimeError('Variable "row" does not exist.', 96, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["column_value"]) {
            // line 97
            echo "        <td>";
            // line 98
            echo $this->extensions['Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension']->renderColumnValue($this->env, $context["column_value"]);
            // line 99
            echo "</td>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['column_value'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 103
    public function block_kreyu_data_table_column_label($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "kreyu_data_table_column_label"));

        // line 104
        echo "    ";
        if ( !((isset($context["translation_domain"]) || array_key_exists("translation_domain", $context) ? $context["translation_domain"] : (function () { throw new RuntimeError('Variable "translation_domain" does not exist.', 104, $this->source); })()) === false)) {
            // line 105
            echo "        <span>";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans((isset($context["label"]) || array_key_exists("label", $context) ? $context["label"] : (function () { throw new RuntimeError('Variable "label" does not exist.', 105, $this->source); })()), (isset($context["translation_parameters"]) || array_key_exists("translation_parameters", $context) ? $context["translation_parameters"] : (function () { throw new RuntimeError('Variable "translation_parameters" does not exist.', 105, $this->source); })()), (isset($context["translation_domain"]) || array_key_exists("translation_domain", $context) ? $context["translation_domain"] : (function () { throw new RuntimeError('Variable "translation_domain" does not exist.', 105, $this->source); })())), "html", null, true);
            echo "</span>
    ";
        } else {
            // line 107
            echo "        <span>";
            echo twig_escape_filter($this->env, (isset($context["label"]) || array_key_exists("label", $context) ? $context["label"] : (function () { throw new RuntimeError('Variable "label" does not exist.', 107, $this->source); })()), "html", null, true);
            echo "</span>
    ";
        }
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 111
    public function block_kreyu_data_table_column_header($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "kreyu_data_table_column_header"));

        // line 112
        echo "    ";
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 112, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 112)->displayBlock((isset($context["block_name"]) || array_key_exists("block_name", $context) ? $context["block_name"] : (function () { throw new RuntimeError('Variable "block_name" does not exist.', 112, $this->source); })()), $context);
        echo "
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 115
    public function block_kreyu_data_table_column_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "kreyu_data_table_column_value"));

        // line 116
        echo "    ";
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 116, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 116)->displayBlock((isset($context["block_name"]) || array_key_exists("block_name", $context) ? $context["block_name"] : (function () { throw new RuntimeError('Variable "block_name" does not exist.', 116, $this->source); })()), $context);
        echo "
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 119
    public function block_kreyu_data_table_action($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "kreyu_data_table_action"));

        // line 120
        echo "    ";
        if ((isset($context["visible"]) || array_key_exists("visible", $context) ? $context["visible"] : (function () { throw new RuntimeError('Variable "visible" does not exist.', 120, $this->source); })())) {
            $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 120, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 120)->displayBlock((isset($context["block_name"]) || array_key_exists("block_name", $context) ? $context["block_name"] : (function () { throw new RuntimeError('Variable "block_name" does not exist.', 120, $this->source); })()), $context);
        }
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 125
    public function block_kreyu_data_table_pagination($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "kreyu_data_table_pagination"));

        // line 126
        echo "    ";
        if (((isset($context["page_count"]) || array_key_exists("page_count", $context) ? $context["page_count"] : (function () { throw new RuntimeError('Variable "page_count" does not exist.', 126, $this->source); })()) > 1)) {
            // line 127
            echo "        ";
            $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 127, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 127)->displayBlock("pagination_widget", $context);
            echo "
    ";
        }
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 131
    public function block_pagination_widget($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_widget"));

        // line 132
        echo "    ";
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 132, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 132)->displayBlock("pagination_counters", $context);
        echo "
    ";
        // line 133
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 133, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 133)->displayBlock("pagination_controls", $context);
        echo "
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 136
    public function block_pagination_controls($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_controls"));

        // line 137
        if ((isset($context["has_previous_page"]) || array_key_exists("has_previous_page", $context) ? $context["has_previous_page"] : (function () { throw new RuntimeError('Variable "has_previous_page" does not exist.', 137, $this->source); })())) {
            // line 138
            $__internal_compile_8 = $context;
            $__internal_compile_9 = ["path" => $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 138, $this->source); })()), "request", [], "any", false, false, false, 138), "get", ["_route"], "method", false, false, false, 138), twig_array_merge(twig_array_merge(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 138, $this->source); })()), "request", [], "any", false, false, false, 138), "attributes", [], "any", false, false, false, 138), "get", ["_route_params"], "method", false, false, false, 138), twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 138, $this->source); })()), "request", [], "any", false, false, false, 138), "query", [], "any", false, false, false, 138), "all", [], "any", false, false, false, 138)), [(isset($context["page_parameter_name"]) || array_key_exists("page_parameter_name", $context) ? $context["page_parameter_name"] : (function () { throw new RuntimeError('Variable "page_parameter_name" does not exist.', 138, $this->source); })()) => 1]))];
            if (!twig_test_iterable($__internal_compile_9)) {
                throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 138, $this->getSourceContext());
            }
            $__internal_compile_9 = twig_to_array($__internal_compile_9);
            $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_9));
            // line 139
            echo "            ";
            $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 139, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 139)->displayBlock("pagination_first", $context);
            echo "
        ";
            $context = $__internal_compile_8;
            // line 141
            echo "
        ";
            // line 142
            $__internal_compile_10 = $context;
            $__internal_compile_11 = ["path" => $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 142, $this->source); })()), "request", [], "any", false, false, false, 142), "get", ["_route"], "method", false, false, false, 142), twig_array_merge(twig_array_merge(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 142, $this->source); })()), "request", [], "any", false, false, false, 142), "attributes", [], "any", false, false, false, 142), "get", ["_route_params"], "method", false, false, false, 142), twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 142, $this->source); })()), "request", [], "any", false, false, false, 142), "query", [], "any", false, false, false, 142), "all", [], "any", false, false, false, 142)), [(isset($context["page_parameter_name"]) || array_key_exists("page_parameter_name", $context) ? $context["page_parameter_name"] : (function () { throw new RuntimeError('Variable "page_parameter_name" does not exist.', 142, $this->source); })()) => ((isset($context["current_page_number"]) || array_key_exists("current_page_number", $context) ? $context["current_page_number"] : (function () { throw new RuntimeError('Variable "current_page_number" does not exist.', 142, $this->source); })()) - 1)]))];
            if (!twig_test_iterable($__internal_compile_11)) {
                throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 142, $this->getSourceContext());
            }
            $__internal_compile_11 = twig_to_array($__internal_compile_11);
            $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_11));
            // line 143
            echo "            ";
            $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 143, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 143)->displayBlock("pagination_previous", $context);
            echo "
        ";
            $context = $__internal_compile_10;
        } else {
            // line 146
            $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 146, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 146)->displayBlock("pagination_first_disabled", $context);
            echo "
        ";
            // line 147
            $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 147, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 147)->displayBlock("pagination_previous_disabled", $context);
        }
        // line 150
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(range((isset($context["first_visible_page_number"]) || array_key_exists("first_visible_page_number", $context) ? $context["first_visible_page_number"] : (function () { throw new RuntimeError('Variable "first_visible_page_number" does not exist.', 150, $this->source); })()), (isset($context["last_visible_page_number"]) || array_key_exists("last_visible_page_number", $context) ? $context["last_visible_page_number"] : (function () { throw new RuntimeError('Variable "last_visible_page_number" does not exist.', 150, $this->source); })())));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["page_number"]) {
            // line 151
            echo "        ";
            if (($context["page_number"] == (isset($context["current_page_number"]) || array_key_exists("current_page_number", $context) ? $context["current_page_number"] : (function () { throw new RuntimeError('Variable "current_page_number" does not exist.', 151, $this->source); })()))) {
                // line 152
                echo "            ";
                $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 152, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 152)->displayBlock("pagination_page_active", $context);
                echo "
        ";
            } else {
                // line 154
                echo "            ";
                $__internal_compile_12 = $context;
                $__internal_compile_13 = ["path" => $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 154, $this->source); })()), "request", [], "any", false, false, false, 154), "get", ["_route"], "method", false, false, false, 154), twig_array_merge(twig_array_merge(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 154, $this->source); })()), "request", [], "any", false, false, false, 154), "attributes", [], "any", false, false, false, 154), "get", ["_route_params"], "method", false, false, false, 154), twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 154, $this->source); })()), "request", [], "any", false, false, false, 154), "query", [], "any", false, false, false, 154), "all", [], "any", false, false, false, 154)), [(isset($context["page_parameter_name"]) || array_key_exists("page_parameter_name", $context) ? $context["page_parameter_name"] : (function () { throw new RuntimeError('Variable "page_parameter_name" does not exist.', 154, $this->source); })()) => $context["page_number"]]))];
                if (!twig_test_iterable($__internal_compile_13)) {
                    throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 154, $this->getSourceContext());
                }
                $__internal_compile_13 = twig_to_array($__internal_compile_13);
                $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_13));
                // line 155
                echo "                ";
                $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 155, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 155)->displayBlock("pagination_page", $context);
                echo "
            ";
                $context = $__internal_compile_12;
                // line 157
                echo "        ";
            }
            // line 158
            echo "    ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['page_number'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 160
        if ((isset($context["has_next_page"]) || array_key_exists("has_next_page", $context) ? $context["has_next_page"] : (function () { throw new RuntimeError('Variable "has_next_page" does not exist.', 160, $this->source); })())) {
            // line 161
            $__internal_compile_14 = $context;
            $__internal_compile_15 = ["path" => $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 161, $this->source); })()), "request", [], "any", false, false, false, 161), "get", ["_route"], "method", false, false, false, 161), twig_array_merge(twig_array_merge(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 161, $this->source); })()), "request", [], "any", false, false, false, 161), "attributes", [], "any", false, false, false, 161), "get", ["_route_params"], "method", false, false, false, 161), twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 161, $this->source); })()), "request", [], "any", false, false, false, 161), "query", [], "any", false, false, false, 161), "all", [], "any", false, false, false, 161)), [(isset($context["page_parameter_name"]) || array_key_exists("page_parameter_name", $context) ? $context["page_parameter_name"] : (function () { throw new RuntimeError('Variable "page_parameter_name" does not exist.', 161, $this->source); })()) => ((isset($context["current_page_number"]) || array_key_exists("current_page_number", $context) ? $context["current_page_number"] : (function () { throw new RuntimeError('Variable "current_page_number" does not exist.', 161, $this->source); })()) + 1)]))];
            if (!twig_test_iterable($__internal_compile_15)) {
                throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 161, $this->getSourceContext());
            }
            $__internal_compile_15 = twig_to_array($__internal_compile_15);
            $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_15));
            // line 162
            echo "            ";
            $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 162, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 162)->displayBlock("pagination_next", $context);
            echo "
        ";
            $context = $__internal_compile_14;
            // line 164
            echo "
        ";
            // line 165
            $__internal_compile_16 = $context;
            $__internal_compile_17 = ["path" => $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 165, $this->source); })()), "request", [], "any", false, false, false, 165), "get", ["_route"], "method", false, false, false, 165), twig_array_merge(twig_array_merge(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 165, $this->source); })()), "request", [], "any", false, false, false, 165), "attributes", [], "any", false, false, false, 165), "get", ["_route_params"], "method", false, false, false, 165), twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 165, $this->source); })()), "request", [], "any", false, false, false, 165), "query", [], "any", false, false, false, 165), "all", [], "any", false, false, false, 165)), [(isset($context["page_parameter_name"]) || array_key_exists("page_parameter_name", $context) ? $context["page_parameter_name"] : (function () { throw new RuntimeError('Variable "page_parameter_name" does not exist.', 165, $this->source); })()) => (isset($context["page_count"]) || array_key_exists("page_count", $context) ? $context["page_count"] : (function () { throw new RuntimeError('Variable "page_count" does not exist.', 165, $this->source); })())]))];
            if (!twig_test_iterable($__internal_compile_17)) {
                throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 165, $this->getSourceContext());
            }
            $__internal_compile_17 = twig_to_array($__internal_compile_17);
            $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_17));
            // line 166
            echo "            ";
            $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 166, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 166)->displayBlock("pagination_last", $context);
            echo "
        ";
            $context = $__internal_compile_16;
        } else {
            // line 169
            $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 169, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 169)->displayBlock("pagination_next_disabled", $context);
            echo "
        ";
            // line 170
            $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 170, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 170)->displayBlock("pagination_last_disabled", $context);
        }
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 174
    public function block_pagination_counters($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_counters"));

        // line 175
        echo "    <span";
        $this->displayBlock("attributes", $context, $blocks);
        echo ">";
        // line 176
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 176, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 176)->displayBlock("pagination_counters_message", $context);
        // line 177
        echo "</span>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 180
    public function block_pagination_counters_message($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_counters_message"));

        // line 181
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("Showing %current_page_first_item_index% - %current_page_last_item_index% of %total_item_count%", ["%current_page_first_item_index%" =>         // line 182
(isset($context["current_page_first_item_index"]) || array_key_exists("current_page_first_item_index", $context) ? $context["current_page_first_item_index"] : (function () { throw new RuntimeError('Variable "current_page_first_item_index" does not exist.', 182, $this->source); })()), "%current_page_last_item_index%" =>         // line 183
(isset($context["current_page_last_item_index"]) || array_key_exists("current_page_last_item_index", $context) ? $context["current_page_last_item_index"] : (function () { throw new RuntimeError('Variable "current_page_last_item_index" does not exist.', 183, $this->source); })()), "%total_item_count%" =>         // line 184
(isset($context["total_item_count"]) || array_key_exists("total_item_count", $context) ? $context["total_item_count"] : (function () { throw new RuntimeError('Variable "total_item_count" does not exist.', 184, $this->source); })())], "KreyuDataTable"), "html", null, true);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 188
    public function block_pagination_page($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_page"));

        // line 189
        echo "    <a ";
        $__internal_compile_18 = $context;
        $__internal_compile_19 = ["attr" => twig_array_merge(((array_key_exists("attr", $context)) ? (_twig_default_filter((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 189, $this->source); })()), [])) : ([])), ["href" => (isset($context["path"]) || array_key_exists("path", $context) ? $context["path"] : (function () { throw new RuntimeError('Variable "path" does not exist.', 189, $this->source); })()), "data-turbo-action" => "advance"])];
        if (!twig_test_iterable($__internal_compile_19)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 189, $this->getSourceContext());
        }
        $__internal_compile_19 = twig_to_array($__internal_compile_19);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_19));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_18;
        echo ">
        ";
        // line 190
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 190, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 190)->displayBlock("pagination_page_message", $context);
        echo "
    </a>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 194
    public function block_pagination_page_active($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_page_active"));

        // line 195
        echo "    <span ";
        $__internal_compile_20 = $context;
        $__internal_compile_21 = ["attr" => twig_array_merge(((array_key_exists("attr", $context)) ? (_twig_default_filter((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 195, $this->source); })()), [])) : ([])), ["data-turbo-action" => "advance"])];
        if (!twig_test_iterable($__internal_compile_21)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 195, $this->getSourceContext());
        }
        $__internal_compile_21 = twig_to_array($__internal_compile_21);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_21));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_20;
        echo ">
        ";
        // line 196
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 196, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 196)->displayBlock("pagination_page_message", $context);
        echo "
    </span>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 200
    public function block_pagination_page_message($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_page_message"));

        echo twig_escape_filter($this->env, (isset($context["page_number"]) || array_key_exists("page_number", $context) ? $context["page_number"] : (function () { throw new RuntimeError('Variable "page_number" does not exist.', 200, $this->source); })()), "html", null, true);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 202
    public function block_pagination_first($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_first"));

        // line 203
        echo "    <a ";
        $__internal_compile_22 = $context;
        $__internal_compile_23 = ["attr" => twig_array_merge(((array_key_exists("attr", $context)) ? (_twig_default_filter((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 203, $this->source); })()), [])) : ([])), ["href" => (isset($context["path"]) || array_key_exists("path", $context) ? $context["path"] : (function () { throw new RuntimeError('Variable "path" does not exist.', 203, $this->source); })()), "data-turbo-action" => "advance"])];
        if (!twig_test_iterable($__internal_compile_23)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 203, $this->getSourceContext());
        }
        $__internal_compile_23 = twig_to_array($__internal_compile_23);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_23));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_22;
        echo ">
        ";
        // line 204
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 204, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 204)->displayBlock("pagination_first_message", $context);
        echo "
    </a>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 208
    public function block_pagination_first_disabled($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_first_disabled"));

        // line 209
        echo "    <span ";
        $__internal_compile_24 = $context;
        $__internal_compile_25 = ["attr" => twig_array_merge(((array_key_exists("attr", $context)) ? (_twig_default_filter((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 209, $this->source); })()), [])) : ([])), ["data-turbo-action" => "advance"])];
        if (!twig_test_iterable($__internal_compile_25)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 209, $this->getSourceContext());
        }
        $__internal_compile_25 = twig_to_array($__internal_compile_25);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_25));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_24;
        echo ">
        ";
        // line 210
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 210, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 210)->displayBlock("pagination_first_message", $context);
        echo "
    </span>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 214
    public function block_pagination_first_message($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_first_message"));

        echo "«";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 216
    public function block_pagination_previous($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_previous"));

        // line 217
        echo "    <a ";
        $__internal_compile_26 = $context;
        $__internal_compile_27 = ["attr" => twig_array_merge(((array_key_exists("attr", $context)) ? (_twig_default_filter((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 217, $this->source); })()), [])) : ([])), ["href" => (isset($context["path"]) || array_key_exists("path", $context) ? $context["path"] : (function () { throw new RuntimeError('Variable "path" does not exist.', 217, $this->source); })()), "data-turbo-action" => "advance"])];
        if (!twig_test_iterable($__internal_compile_27)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 217, $this->getSourceContext());
        }
        $__internal_compile_27 = twig_to_array($__internal_compile_27);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_27));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_26;
        echo ">
        ";
        // line 218
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 218, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 218)->displayBlock("pagination_previous_message", $context);
        echo "
    </a>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 222
    public function block_pagination_previous_disabled($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_previous_disabled"));

        // line 223
        echo "    <span ";
        $this->displayBlock("attributes", $context, $blocks);
        echo ">";
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 223, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 223)->displayBlock("pagination_previous_message", $context);
        echo "</span>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 226
    public function block_pagination_previous_message($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_previous_message"));

        echo "‹";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 228
    public function block_pagination_last($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_last"));

        // line 229
        echo "    <a ";
        $__internal_compile_28 = $context;
        $__internal_compile_29 = ["attr" => twig_array_merge(((array_key_exists("attr", $context)) ? (_twig_default_filter((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 229, $this->source); })()), [])) : ([])), ["href" => (isset($context["path"]) || array_key_exists("path", $context) ? $context["path"] : (function () { throw new RuntimeError('Variable "path" does not exist.', 229, $this->source); })()), "data-turbo-action" => "advance"])];
        if (!twig_test_iterable($__internal_compile_29)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 229, $this->getSourceContext());
        }
        $__internal_compile_29 = twig_to_array($__internal_compile_29);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_29));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_28;
        echo ">
        ";
        // line 230
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 230, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 230)->displayBlock("pagination_last_message", $context);
        echo "
    </a>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 234
    public function block_pagination_last_disabled($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_last_disabled"));

        // line 235
        echo "    <span ";
        $this->displayBlock("attributes", $context, $blocks);
        echo ">";
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 235, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 235)->displayBlock("pagination_last_message", $context);
        echo "</span>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 238
    public function block_pagination_last_message($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_last_message"));

        echo "»";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 240
    public function block_pagination_next($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_next"));

        // line 241
        echo "    <a ";
        $__internal_compile_30 = $context;
        $__internal_compile_31 = ["attr" => twig_array_merge(((array_key_exists("attr", $context)) ? (_twig_default_filter((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 241, $this->source); })()), [])) : ([])), ["href" => (isset($context["path"]) || array_key_exists("path", $context) ? $context["path"] : (function () { throw new RuntimeError('Variable "path" does not exist.', 241, $this->source); })()), "data-turbo-action" => "advance"])];
        if (!twig_test_iterable($__internal_compile_31)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 241, $this->getSourceContext());
        }
        $__internal_compile_31 = twig_to_array($__internal_compile_31);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_31));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_30;
        echo ">
        ";
        // line 242
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 242, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 242)->displayBlock("pagination_next_message", $context);
        echo "
    </a>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 246
    public function block_pagination_next_disabled($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_next_disabled"));

        // line 247
        echo "    <span ";
        $this->displayBlock("attributes", $context, $blocks);
        echo ">";
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 247, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 247)->displayBlock("pagination_next_message", $context);
        echo "</span>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 250
    public function block_pagination_next_message($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "pagination_next_message"));

        echo "›";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 254
    public function block_kreyu_data_table_filters_form($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "kreyu_data_table_filters_form"));

        // line 255
        echo "    ";
        $this->env->getRuntime("Symfony\\Component\\Form\\FormRenderer")->setTheme((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 255, $this->source); })()), ((array_key_exists("form_themes", $context)) ? (_twig_default_filter((isset($context["form_themes"]) || array_key_exists("form_themes", $context) ? $context["form_themes"] : (function () { throw new RuntimeError('Variable "form_themes" does not exist.', 255, $this->source); })()), [$this->getTemplateName()])) : ([$this->getTemplateName()])), true);
        // line 256
        echo "
    ";
        // line 257
        echo         $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderBlock((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 257, $this->source); })()), 'form_start', ["attr" => ["data-turbo-action" => "advance", "hidden" => "hidden"]]);
        echo "
        ";
        // line 259
        echo "    ";
        echo         $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderBlock((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 259, $this->source); })()), 'form_end', ["render_rest" => false]);
        echo "

    ";
        // line 261
        if ((twig_get_attribute($this->env, $this->source, (isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 261, $this->source); })()), "count", [], "any", false, false, false, 261) > 0)) {
            // line 262
            echo "        ";
            $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 262, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 262)->displayBlock("filtration_widget", $context);
            echo "
    ";
        }
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 266
    public function block_filtration_widget($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "filtration_widget"));

        // line 267
        echo "    <div ";
        $this->displayBlock("attributes", $context, $blocks);
        echo ">";
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 267, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 267)->displayBlock("filtration_form", $context);
        echo "</div>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 270
    public function block_filtration_form($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "filtration_form"));

        // line 271
        echo "    ";
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 271, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 271)->displayBlock("filtration_form_content", $context);
        echo "
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 274
    public function block_filtration_form_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "filtration_form_content"));

        // line 275
        echo "    <div ";
        $this->displayBlock("attributes", $context, $blocks);
        echo ">
        ";
        // line 276
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 276, $this->source); })()));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
            // line 277
            echo "            ";
            $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 277, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 277)->displayBlock("filtration_form_row", $context);
            echo "
        ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 279
        echo "
        ";
        // line 280
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 280, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 280)->displayBlock("filtration_form_submit", $context);
        echo "
    </div>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 284
    public function block_filtration_form_row($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "filtration_form_row"));

        // line 285
        echo "    <div ";
        $this->displayBlock("attributes", $context, $blocks);
        echo ">";
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock((isset($context["child"]) || array_key_exists("child", $context) ? $context["child"] : (function () { throw new RuntimeError('Variable "child" does not exist.', 285, $this->source); })()), 'row');
        echo "</div>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 288
    public function block_filtration_form_submit($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "filtration_form_submit"));

        // line 289
        echo "    <button ";
        $__internal_compile_32 = $context;
        $__internal_compile_33 = ["attr" => twig_array_merge(((array_key_exists("attr", $context)) ? (_twig_default_filter((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 289, $this->source); })()), [])) : ([])), ["form" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 289, $this->source); })()), "vars", [], "any", false, false, false, 289), "id", [], "any", false, false, false, 289), "data-turbo-action" => "advance"])];
        if (!twig_test_iterable($__internal_compile_33)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 289, $this->getSourceContext());
        }
        $__internal_compile_33 = twig_to_array($__internal_compile_33);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_33));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_32;
        echo ">
        ";
        // line 290
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("Filter", [], "KreyuDataTable"), "html", null, true);
        echo "
    </button>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 294
    public function block_kreyu_data_table_date_range_widget($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "kreyu_data_table_date_range_widget"));

        // line 295
        echo "    ";
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(twig_get_attribute($this->env, $this->source, (isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 295, $this->source); })()), "from", [], "any", false, false, false, 295), 'widget');
        echo "
    ";
        // line 296
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(twig_get_attribute($this->env, $this->source, (isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 296, $this->source); })()), "to", [], "any", false, false, false, 296), 'widget');
        echo "
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 301
    public function block_column_header($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_header"));

        // line 302
        echo "    ";
        $context["label_attr"] = ((array_key_exists("label_attr", $context)) ? (_twig_default_filter((isset($context["label_attr"]) || array_key_exists("label_attr", $context) ? $context["label_attr"] : (function () { throw new RuntimeError('Variable "label_attr" does not exist.', 302, $this->source); })()), [])) : ([]));
        // line 303
        echo "
    ";
        // line 304
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["data_table"]) || array_key_exists("data_table", $context) ? $context["data_table"] : (function () { throw new RuntimeError('Variable "data_table" does not exist.', 304, $this->source); })()), "vars", [], "any", false, false, false, 304), "sorting_enabled", [], "any", false, false, false, 304) && (isset($context["sortable"]) || array_key_exists("sortable", $context) ? $context["sortable"] : (function () { throw new RuntimeError('Variable "sortable" does not exist.', 304, $this->source); })()))) {
            // line 305
            echo "        ";
            $context["current_sort_field"] = ((twig_get_attribute($this->env, $this->source, ($context["sorting_field_data"] ?? null), "name", [], "any", true, true, false, 305)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["sorting_field_data"] ?? null), "name", [], "any", false, false, false, 305), null)) : (null));
            // line 306
            echo "        ";
            $context["current_sort_direction"] = ((twig_get_attribute($this->env, $this->source, ($context["sorting_field_data"] ?? null), "direction", [], "any", true, true, false, 306)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["sorting_field_data"] ?? null), "direction", [], "any", false, false, false, 306), null)) : (null));
            // line 307
            echo "
        ";
            // line 308
            $context["active_attr"] = ((array_key_exists("active_attr", $context)) ? (_twig_default_filter((isset($context["active_attr"]) || array_key_exists("active_attr", $context) ? $context["active_attr"] : (function () { throw new RuntimeError('Variable "active_attr" does not exist.', 308, $this->source); })()), [])) : ([]));
            // line 309
            echo "        ";
            $context["inactive_attr"] = ((array_key_exists("inactive_attr", $context)) ? (_twig_default_filter((isset($context["inactive_attr"]) || array_key_exists("inactive_attr", $context) ? $context["inactive_attr"] : (function () { throw new RuntimeError('Variable "inactive_attr" does not exist.', 309, $this->source); })()), [])) : ([]));
            // line 310
            echo "
        ";
            // line 311
            $context["attr"] = ((array_key_exists("attr", $context)) ? (_twig_default_filter((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 311, $this->source); })()), [])) : ([]));
            // line 312
            echo "        ";
            $context["attr"] = twig_array_merge((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 312, $this->source); })()), (((isset($context["sorted"]) || array_key_exists("sorted", $context) ? $context["sorted"] : (function () { throw new RuntimeError('Variable "sorted" does not exist.', 312, $this->source); })())) ? ((isset($context["active_attr"]) || array_key_exists("active_attr", $context) ? $context["active_attr"] : (function () { throw new RuntimeError('Variable "active_attr" does not exist.', 312, $this->source); })())) : ((isset($context["inactive_attr"]) || array_key_exists("inactive_attr", $context) ? $context["inactive_attr"] : (function () { throw new RuntimeError('Variable "inactive_attr" does not exist.', 312, $this->source); })()))));
            // line 313
            echo "
        <th ";
            // line 314
            $this->displayBlock("attributes", $context, $blocks);
            echo ">
            ";
            // line 315
            $context["query_params"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 315, $this->source); })()), "request", [], "any", false, false, false, 315), "query", [], "any", false, false, false, 315), "all", [], "any", false, false, false, 315);
            // line 316
            echo "            ";
            $context["query_params"] = twig_array_merge((isset($context["query_params"]) || array_key_exists("query_params", $context) ? $context["query_params"] : (function () { throw new RuntimeError('Variable "query_params" does not exist.', 316, $this->source); })()), [(isset($context["sort_parameter_name"]) || array_key_exists("sort_parameter_name", $context) ? $context["sort_parameter_name"] : (function () { throw new RuntimeError('Variable "sort_parameter_name" does not exist.', 316, $this->source); })()) => [            // line 317
(isset($context["name"]) || array_key_exists("name", $context) ? $context["name"] : (function () { throw new RuntimeError('Variable "name" does not exist.', 317, $this->source); })()) => (((twig_lower_filter($this->env, (isset($context["sort_direction"]) || array_key_exists("sort_direction", $context) ? $context["sort_direction"] : (function () { throw new RuntimeError('Variable "sort_direction" does not exist.', 317, $this->source); })())) == "desc")) ? ("asc") : ("desc"))]]);
            // line 319
            echo "
            ";
            // line 320
            $context["query_params"] = twig_array_merge(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 320, $this->source); })()), "request", [], "any", false, false, false, 320), "attributes", [], "any", false, false, false, 320), "get", ["_route_params"], "method", false, false, false, 320), (isset($context["query_params"]) || array_key_exists("query_params", $context) ? $context["query_params"] : (function () { throw new RuntimeError('Variable "query_params" does not exist.', 320, $this->source); })()));
            // line 321
            echo "
            ";
            // line 322
            $context["label_attr"] = twig_array_merge(["href" => $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 322, $this->source); })()), "request", [], "any", false, false, false, 322), "get", ["_route"], "method", false, false, false, 322), (isset($context["query_params"]) || array_key_exists("query_params", $context) ? $context["query_params"] : (function () { throw new RuntimeError('Variable "query_params" does not exist.', 322, $this->source); })()))], (isset($context["label_attr"]) || array_key_exists("label_attr", $context) ? $context["label_attr"] : (function () { throw new RuntimeError('Variable "label_attr" does not exist.', 322, $this->source); })()));
            // line 323
            echo "            ";
            $context["label_attr"] = twig_array_merge(["data-turbo-action" => "advance"], (isset($context["label_attr"]) || array_key_exists("label_attr", $context) ? $context["label_attr"] : (function () { throw new RuntimeError('Variable "label_attr" does not exist.', 323, $this->source); })()));
            // line 324
            echo "
            <a ";
            // line 325
            $__internal_compile_34 = $context;
            $__internal_compile_35 = ["attr" => (isset($context["label_attr"]) || array_key_exists("label_attr", $context) ? $context["label_attr"] : (function () { throw new RuntimeError('Variable "label_attr" does not exist.', 325, $this->source); })())];
            if (!twig_test_iterable($__internal_compile_35)) {
                throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 325, $this->getSourceContext());
            }
            $__internal_compile_35 = twig_to_array($__internal_compile_35);
            $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_35));
            $this->displayBlock("attributes", $context, $blocks);
            $context = $__internal_compile_34;
            echo ">";
            // line 326
            $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 326, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 326)->displayBlock("column_header_label", $context);
            // line 328
            if ((isset($context["sorted"]) || array_key_exists("sorted", $context) ? $context["sorted"] : (function () { throw new RuntimeError('Variable "sorted" does not exist.', 328, $this->source); })())) {
                // line 329
                echo "                    ";
                if (((isset($context["sort_direction"]) || array_key_exists("sort_direction", $context) ? $context["sort_direction"] : (function () { throw new RuntimeError('Variable "sort_direction" does not exist.', 329, $this->source); })()) == "asc")) {
                    // line 330
                    echo "                        ";
                    $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 330, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 330)->displayBlock("sort_arrow_asc", $context);
                    echo "
                    ";
                } else {
                    // line 332
                    echo "                        ";
                    $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 332, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 332)->displayBlock("sort_arrow_desc", $context);
                    echo "
                    ";
                }
                // line 334
                echo "                ";
            } else {
                // line 335
                echo "                    ";
                $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 335, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 335)->displayBlock("sort_arrow_none", $context);
                echo "
                ";
            }
            // line 337
            echo "            </a>
        </th>
    ";
        } else {
            // line 340
            echo "        <th ";
            $this->displayBlock("attributes", $context, $blocks);
            echo ">
            <span ";
            // line 341
            $__internal_compile_36 = $context;
            $__internal_compile_37 = ["attr" => (isset($context["label_attr"]) || array_key_exists("label_attr", $context) ? $context["label_attr"] : (function () { throw new RuntimeError('Variable "label_attr" does not exist.', 341, $this->source); })())];
            if (!twig_test_iterable($__internal_compile_37)) {
                throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 341, $this->getSourceContext());
            }
            $__internal_compile_37 = twig_to_array($__internal_compile_37);
            $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_37));
            $this->displayBlock("attributes", $context, $blocks);
            $context = $__internal_compile_36;
            echo ">";
            // line 342
            $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 342, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 342)->displayBlock("column_header_label", $context);
            // line 343
            echo "</span>
        </th>
    ";
        }
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 348
    public function block_column_header_label($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_header_label"));

        // line 349
        echo "    ";
        if ( !((isset($context["translation_domain"]) || array_key_exists("translation_domain", $context) ? $context["translation_domain"] : (function () { throw new RuntimeError('Variable "translation_domain" does not exist.', 349, $this->source); })()) === false)) {
            // line 350
            echo "        <span>";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans((isset($context["label"]) || array_key_exists("label", $context) ? $context["label"] : (function () { throw new RuntimeError('Variable "label" does not exist.', 350, $this->source); })()), (isset($context["translation_parameters"]) || array_key_exists("translation_parameters", $context) ? $context["translation_parameters"] : (function () { throw new RuntimeError('Variable "translation_parameters" does not exist.', 350, $this->source); })()), (isset($context["translation_domain"]) || array_key_exists("translation_domain", $context) ? $context["translation_domain"] : (function () { throw new RuntimeError('Variable "translation_domain" does not exist.', 350, $this->source); })())), "html", null, true);
            echo "</span>
    ";
        } else {
            // line 352
            echo "        <span>";
            echo twig_escape_filter($this->env, (isset($context["label"]) || array_key_exists("label", $context) ? $context["label"] : (function () { throw new RuntimeError('Variable "label" does not exist.', 352, $this->source); })()), "html", null, true);
            echo "</span>
    ";
        }
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 358
    public function block_column_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_value"));

        // line 359
        echo "    <span";
        $this->displayBlock("attributes", $context, $blocks);
        echo ">";
        // line 360
        if ( !((isset($context["translation_domain"]) || array_key_exists("translation_domain", $context) ? $context["translation_domain"] : (function () { throw new RuntimeError('Variable "translation_domain" does not exist.', 360, $this->source); })()) === false)) {
            // line 361
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans((isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 361, $this->source); })()), [], (isset($context["translation_domain"]) || array_key_exists("translation_domain", $context) ? $context["translation_domain"] : (function () { throw new RuntimeError('Variable "translation_domain" does not exist.', 361, $this->source); })())), "html", null, true);
        } else {
            // line 363
            echo twig_escape_filter($this->env, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 363, $this->source); })()), "html", null, true);
        }
        // line 365
        echo "</span>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 368
    public function block_column_text_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_text_value"));

        // line 369
        $this->displayBlock("column_value", $context, $blocks);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 372
    public function block_column_number_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_number_value"));

        // line 373
        $this->displayBlock("column_text_value", $context, $blocks);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 376
    public function block_column_money_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_money_value"));

        // line 377
        $this->displayBlock("column_text_value", $context, $blocks);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 380
    public function block_column_link_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_link_value"));

        // line 381
        echo "    <a ";
        $__internal_compile_38 = $context;
        $__internal_compile_39 = ["attr" => twig_array_merge(["href" => (isset($context["href"]) || array_key_exists("href", $context) ? $context["href"] : (function () { throw new RuntimeError('Variable "href" does not exist.', 381, $this->source); })()), "target" => (isset($context["target"]) || array_key_exists("target", $context) ? $context["target"] : (function () { throw new RuntimeError('Variable "target" does not exist.', 381, $this->source); })())], (isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 381, $this->source); })()))];
        if (!twig_test_iterable($__internal_compile_39)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 381, $this->getSourceContext());
        }
        $__internal_compile_39 = twig_to_array($__internal_compile_39);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_39));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_38;
        echo ">";
        // line 382
        $this->displayBlock("column_text_value", $context, $blocks);
        // line 383
        echo "</a>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 386
    public function block_column_date_time_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_date_time_value"));

        // line 387
        echo "    ";
        $__internal_compile_40 = $context;
        $__internal_compile_41 = ["value" => (((isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 387, $this->source); })())) ? (twig_date_format_filter($this->env, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 387, $this->source); })()), (isset($context["format"]) || array_key_exists("format", $context) ? $context["format"] : (function () { throw new RuntimeError('Variable "format" does not exist.', 387, $this->source); })()), (isset($context["timezone"]) || array_key_exists("timezone", $context) ? $context["timezone"] : (function () { throw new RuntimeError('Variable "timezone" does not exist.', 387, $this->source); })()))) : ((isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 387, $this->source); })())))];
        if (!twig_test_iterable($__internal_compile_41)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 387, $this->getSourceContext());
        }
        $__internal_compile_41 = twig_to_array($__internal_compile_41);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_41));
        // line 388
        $this->displayBlock("column_text_value", $context, $blocks);
        $context = $__internal_compile_40;
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 392
    public function block_column_date_period_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_date_period_value"));

        // line 393
        echo "    ";
        $__internal_compile_42 = $context;
        $__internal_compile_43 = ["value" => (((isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 393, $this->source); })())) ? (((twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 393, $this->source); })()), "start", [], "any", false, false, false, 393), (isset($context["format"]) || array_key_exists("format", $context) ? $context["format"] : (function () { throw new RuntimeError('Variable "format" does not exist.', 393, $this->source); })()), (isset($context["timezone"]) || array_key_exists("timezone", $context) ? $context["timezone"] : (function () { throw new RuntimeError('Variable "timezone" does not exist.', 393, $this->source); })())) . (isset($context["separator"]) || array_key_exists("separator", $context) ? $context["separator"] : (function () { throw new RuntimeError('Variable "separator" does not exist.', 393, $this->source); })())) . twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 393, $this->source); })()), "end", [], "any", false, false, false, 393), (isset($context["format"]) || array_key_exists("format", $context) ? $context["format"] : (function () { throw new RuntimeError('Variable "format" does not exist.', 393, $this->source); })()), (isset($context["timezone"]) || array_key_exists("timezone", $context) ? $context["timezone"] : (function () { throw new RuntimeError('Variable "timezone" does not exist.', 393, $this->source); })())))) : ((isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 393, $this->source); })())))];
        if (!twig_test_iterable($__internal_compile_43)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 393, $this->getSourceContext());
        }
        $__internal_compile_43 = twig_to_array($__internal_compile_43);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_43));
        // line 394
        $this->displayBlock("column_text_value", $context, $blocks);
        $context = $__internal_compile_42;
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 398
    public function block_column_boolean_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_boolean_value"));

        // line 399
        echo "    ";
        $__internal_compile_44 = $context;
        $__internal_compile_45 = ["value" => (((isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 399, $this->source); })())) ? ((isset($context["label_true"]) || array_key_exists("label_true", $context) ? $context["label_true"] : (function () { throw new RuntimeError('Variable "label_true" does not exist.', 399, $this->source); })())) : ((isset($context["label_false"]) || array_key_exists("label_false", $context) ? $context["label_false"] : (function () { throw new RuntimeError('Variable "label_false" does not exist.', 399, $this->source); })())))];
        if (!twig_test_iterable($__internal_compile_45)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 399, $this->getSourceContext());
        }
        $__internal_compile_45 = twig_to_array($__internal_compile_45);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_45));
        // line 400
        $this->displayBlock("column_text_value", $context, $blocks);
        $context = $__internal_compile_44;
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 404
    public function block_column_collection_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_collection_value"));

        // line 405
        ob_start();
        // line 406
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["children"]) || array_key_exists("children", $context) ? $context["children"] : (function () { throw new RuntimeError('Variable "children" does not exist.', 406, $this->source); })()));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
            // line 407
            echo $this->extensions['Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension']->renderColumnValue($this->env, $context["child"]);
            // line 408
            if ( !twig_get_attribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 408)) {
                $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 408, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 408)->displayBlock("column_collection_separator", $context);
            }
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        $___internal_parse_0_ = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 405
        echo twig_spaceless($___internal_parse_0_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 413
    public function block_column_collection_separator($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_collection_separator"));

        // line 414
        echo "    <span>";
        echo twig_escape_filter($this->env, (isset($context["separator"]) || array_key_exists("separator", $context) ? $context["separator"] : (function () { throw new RuntimeError('Variable "separator" does not exist.', 414, $this->source); })()), "html", null, true);
        echo "</span>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 417
    public function block_column_template_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_template_value"));

        // line 418
        echo twig_include($this->env, $context, (isset($context["template_path"]) || array_key_exists("template_path", $context) ? $context["template_path"] : (function () { throw new RuntimeError('Variable "template_path" does not exist.', 418, $this->source); })()), (isset($context["template_vars"]) || array_key_exists("template_vars", $context) ? $context["template_vars"] : (function () { throw new RuntimeError('Variable "template_vars" does not exist.', 418, $this->source); })()));
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 421
    public function block_column_actions_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_actions_value"));

        // line 422
        echo "    ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["actions"]) || array_key_exists("actions", $context) ? $context["actions"] : (function () { throw new RuntimeError('Variable "actions" does not exist.', 422, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["action"]) {
            // line 423
            echo "        ";
            echo $this->extensions['Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension']->renderAction($this->env, $context["action"]);
            echo "
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['action'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 427
    public function block_column_form_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_form_value"));

        // line 428
        echo "    ";
        if ( !((isset($context["form_child_path"]) || array_key_exists("form_child_path", $context) ? $context["form_child_path"] : (function () { throw new RuntimeError('Variable "form_child_path" does not exist.', 428, $this->source); })()) === false)) {
            // line 429
            echo "        ";
            $context["form"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 429, $this->source); })()), (isset($context["row_index"]) || array_key_exists("row_index", $context) ? $context["row_index"] : (function () { throw new RuntimeError('Variable "row_index" does not exist.', 429, $this->source); })()), [], "array", false, false, false, 429), (isset($context["form_child_path"]) || array_key_exists("form_child_path", $context) ? $context["form_child_path"] : (function () { throw new RuntimeError('Variable "form_child_path" does not exist.', 429, $this->source); })()), [], "array", false, false, false, 429), "createView", [], "method", false, false, false, 429);
            // line 430
            echo "    ";
        } else {
            // line 431
            echo "        ";
            $context["form"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 431, $this->source); })()), (isset($context["row_index"]) || array_key_exists("row_index", $context) ? $context["row_index"] : (function () { throw new RuntimeError('Variable "row_index" does not exist.', 431, $this->source); })()), [], "array", false, false, false, 431), "createView", [], "method", false, false, false, 431);
            // line 432
            echo "    ";
        }
        // line 433
        echo "
    ";
        // line 434
        $context["form_themes"] = ((array_key_exists("form_themes", $context)) ? (_twig_default_filter((isset($context["form_themes"]) || array_key_exists("form_themes", $context) ? $context["form_themes"] : (function () { throw new RuntimeError('Variable "form_themes" does not exist.', 434, $this->source); })()), null)) : (null));
        // line 435
        echo "
    ";
        // line 436
        if ( !(null === (isset($context["form_themes"]) || array_key_exists("form_themes", $context) ? $context["form_themes"] : (function () { throw new RuntimeError('Variable "form_themes" does not exist.', 436, $this->source); })()))) {
            // line 437
            echo "        ";
            $this->env->getRuntime("Symfony\\Component\\Form\\FormRenderer")->setTheme((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 437, $this->source); })()), (isset($context["form_themes"]) || array_key_exists("form_themes", $context) ? $context["form_themes"] : (function () { throw new RuntimeError('Variable "form_themes" does not exist.', 437, $this->source); })()), true);
            // line 438
            echo "    ";
        }
        // line 439
        echo "
    ";
        // line 440
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 440, $this->source); })()), 'widget');
        echo "
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 443
    public function block_column_checkbox_header($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_checkbox_header"));

        // line 444
        echo "    <th ";
        $this->displayBlock("attributes", $context, $blocks);
        echo ">
        ";
        // line 445
        $context["input_attr"] = twig_array_merge(["type" => "checkbox", "aria-label" => "Select all checkbox", "data-identifier-name" =>         // line 448
(isset($context["identifier_name"]) || array_key_exists("identifier_name", $context) ? $context["identifier_name"] : (function () { throw new RuntimeError('Variable "identifier_name" does not exist.', 448, $this->source); })()), "data-kreyu--data-table-bundle--batch-target" => "selectAllCheckbox", "data-action" => "input->kreyu--data-table-bundle--batch#selectAll"], ((        // line 451
array_key_exists("input_attr", $context)) ? (_twig_default_filter((isset($context["input_attr"]) || array_key_exists("input_attr", $context) ? $context["input_attr"] : (function () { throw new RuntimeError('Variable "input_attr" does not exist.', 451, $this->source); })()), [])) : ([])));
        // line 452
        echo "
        <input ";
        // line 453
        $__internal_compile_46 = $context;
        $__internal_compile_47 = ["attr" => (isset($context["input_attr"]) || array_key_exists("input_attr", $context) ? $context["input_attr"] : (function () { throw new RuntimeError('Variable "input_attr" does not exist.', 453, $this->source); })())];
        if (!twig_test_iterable($__internal_compile_47)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 453, $this->getSourceContext());
        }
        $__internal_compile_47 = twig_to_array($__internal_compile_47);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_47));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_46;
        echo ">
    </th>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 457
    public function block_column_checkbox_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "column_checkbox_value"));

        // line 458
        echo "    ";
        $context["input_attr"] = twig_array_merge(["type" => "checkbox", "value" =>         // line 460
(isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 460, $this->source); })()), "aria-label" => "Select all checkbox", "data-index" => twig_get_attribute($this->env, $this->source,         // line 462
(isset($context["row"]) || array_key_exists("row", $context) ? $context["row"] : (function () { throw new RuntimeError('Variable "row" does not exist.', 462, $this->source); })()), "index", [], "any", false, false, false, 462), "data-identifier-name" =>         // line 463
(isset($context["identifier_name"]) || array_key_exists("identifier_name", $context) ? $context["identifier_name"] : (function () { throw new RuntimeError('Variable "identifier_name" does not exist.', 463, $this->source); })()), "data-kreyu--data-table-bundle--batch-target" => "selectRowCheckbox", "data-action" => "input->kreyu--data-table-bundle--batch#selectRow"], ((        // line 466
array_key_exists("input_attr", $context)) ? (_twig_default_filter((isset($context["input_attr"]) || array_key_exists("input_attr", $context) ? $context["input_attr"] : (function () { throw new RuntimeError('Variable "input_attr" does not exist.', 466, $this->source); })()), [])) : ([])));
        // line 467
        echo "
    <input ";
        // line 468
        $__internal_compile_48 = $context;
        $__internal_compile_49 = ["attr" => (isset($context["input_attr"]) || array_key_exists("input_attr", $context) ? $context["input_attr"] : (function () { throw new RuntimeError('Variable "input_attr" does not exist.', 468, $this->source); })())];
        if (!twig_test_iterable($__internal_compile_49)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 468, $this->getSourceContext());
        }
        $__internal_compile_49 = twig_to_array($__internal_compile_49);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_49));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_48;
        echo ">
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 473
    public function block_action_value_icon($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "action_value_icon"));

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 475
    public function block_action_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "action_value"));

        // line 476
        echo "    ";
        if ((isset($context["icon_attr"]) || array_key_exists("icon_attr", $context) ? $context["icon_attr"] : (function () { throw new RuntimeError('Variable "icon_attr" does not exist.', 476, $this->source); })())) {
            $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 476, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 476)->displayBlock("action_value_icon", $context);
        }
        // line 477
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans((isset($context["label"]) || array_key_exists("label", $context) ? $context["label"] : (function () { throw new RuntimeError('Variable "label" does not exist.', 477, $this->source); })()), (isset($context["translation_parameters"]) || array_key_exists("translation_parameters", $context) ? $context["translation_parameters"] : (function () { throw new RuntimeError('Variable "translation_parameters" does not exist.', 477, $this->source); })()), (isset($context["translation_domain"]) || array_key_exists("translation_domain", $context) ? $context["translation_domain"] : (function () { throw new RuntimeError('Variable "translation_domain" does not exist.', 477, $this->source); })())), "html", null, true);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 480
    public function block_action_link_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "action_link_value"));

        // line 481
        echo "    <a ";
        $__internal_compile_50 = $context;
        $__internal_compile_51 = ["attr" => twig_array_merge(["href" => (isset($context["href"]) || array_key_exists("href", $context) ? $context["href"] : (function () { throw new RuntimeError('Variable "href" does not exist.', 481, $this->source); })()), "target" => (isset($context["target"]) || array_key_exists("target", $context) ? $context["target"] : (function () { throw new RuntimeError('Variable "target" does not exist.', 481, $this->source); })())], (isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 481, $this->source); })()))];
        if (!twig_test_iterable($__internal_compile_51)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 481, $this->getSourceContext());
        }
        $__internal_compile_51 = twig_to_array($__internal_compile_51);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_51));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_50;
        echo ">
        ";
        // line 482
        $__internal_compile_52 = $context;
        $__internal_compile_53 = ["attr" => []];
        if (!twig_test_iterable($__internal_compile_53)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 482, $this->getSourceContext());
        }
        $__internal_compile_53 = twig_to_array($__internal_compile_53);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_53));
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 482, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 482)->displayBlock("action_value", $context);
        $context = $__internal_compile_52;
        // line 483
        echo "    </a>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 486
    public function block_action_button_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "action_button_value"));

        // line 487
        echo "    ";
        $context["attr"] = twig_array_merge(["href" => (isset($context["href"]) || array_key_exists("href", $context) ? $context["href"] : (function () { throw new RuntimeError('Variable "href" does not exist.', 487, $this->source); })()), "target" => (isset($context["target"]) || array_key_exists("target", $context) ? $context["target"] : (function () { throw new RuntimeError('Variable "target" does not exist.', 487, $this->source); })())], ((array_key_exists("attr", $context)) ? (_twig_default_filter((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 487, $this->source); })()), [])) : ([])));
        // line 488
        echo "
    ";
        // line 489
        if ((isset($context["batch"]) || array_key_exists("batch", $context) ? $context["batch"] : (function () { throw new RuntimeError('Variable "batch" does not exist.', 489, $this->source); })())) {
            // line 490
            echo "        ";
            $context["attr"] = twig_array_merge(["data-kreyu--data-table-bundle--batch-target" => "identifierHolder"], (isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 490, $this->source); })()));
            // line 491
            echo "    ";
        }
        // line 492
        echo "
    <a ";
        // line 493
        $__internal_compile_54 = $context;
        $__internal_compile_55 = ["attr" => (isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 493, $this->source); })())];
        if (!twig_test_iterable($__internal_compile_55)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 493, $this->getSourceContext());
        }
        $__internal_compile_55 = twig_to_array($__internal_compile_55);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_55));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_54;
        echo ">
        ";
        // line 494
        $__internal_compile_56 = $context;
        $__internal_compile_57 = ["attr" => []];
        if (!twig_test_iterable($__internal_compile_57)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 494, $this->getSourceContext());
        }
        $__internal_compile_57 = twig_to_array($__internal_compile_57);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_57));
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 494, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 494)->displayBlock("action_value", $context);
        $context = $__internal_compile_56;
        // line 495
        echo "    </a>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 498
    public function block_action_form_value($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "action_form_value"));

        // line 499
        echo "    ";
        $context["attr"] = twig_array_merge(["action" => (isset($context["action"]) || array_key_exists("action", $context) ? $context["action"] : (function () { throw new RuntimeError('Variable "action" does not exist.', 499, $this->source); })()), "method" => (isset($context["html_friendly_method"]) || array_key_exists("html_friendly_method", $context) ? $context["html_friendly_method"] : (function () { throw new RuntimeError('Variable "html_friendly_method" does not exist.', 499, $this->source); })())], ((array_key_exists("attr", $context)) ? (_twig_default_filter((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 499, $this->source); })()), [])) : ([])));
        // line 500
        echo "
    ";
        // line 501
        if ((isset($context["batch"]) || array_key_exists("batch", $context) ? $context["batch"] : (function () { throw new RuntimeError('Variable "batch" does not exist.', 501, $this->source); })())) {
            // line 502
            echo "        ";
            $context["attr"] = twig_array_merge(["data-kreyu--data-table-bundle--batch-target" => "identifierHolder"], (isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 502, $this->source); })()));
            // line 503
            echo "    ";
        }
        // line 504
        echo "
    <form id=\"";
        // line 505
        echo twig_escape_filter($this->env, (isset($context["form_id"]) || array_key_exists("form_id", $context) ? $context["form_id"] : (function () { throw new RuntimeError('Variable "form_id" does not exist.', 505, $this->source); })()), "html", null, true);
        echo "\"";
        $this->displayBlock("attributes", $context, $blocks);
        echo ">
        <input type=\"hidden\" name=\"_method\" value=\"";
        // line 506
        echo twig_escape_filter($this->env, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 506, $this->source); })()), "html", null, true);
        echo "\"/>

        ";
        // line 508
        $context["button_tag"] = ((array_key_exists("button_tag", $context)) ? (_twig_default_filter((isset($context["button_tag"]) || array_key_exists("button_tag", $context) ? $context["button_tag"] : (function () { throw new RuntimeError('Variable "button_tag" does not exist.', 508, $this->source); })()), "button")) : ("button"));
        // line 509
        echo "
        <";
        // line 510
        echo twig_escape_filter($this->env, (isset($context["button_tag"]) || array_key_exists("button_tag", $context) ? $context["button_tag"] : (function () { throw new RuntimeError('Variable "button_tag" does not exist.', 510, $this->source); })()), "html", null, true);
        echo " ";
        $__internal_compile_58 = $context;
        $__internal_compile_59 = ["attr" => twig_array_merge(["type" => "submit"], (isset($context["button_attr"]) || array_key_exists("button_attr", $context) ? $context["button_attr"] : (function () { throw new RuntimeError('Variable "button_attr" does not exist.', 510, $this->source); })()))];
        if (!twig_test_iterable($__internal_compile_59)) {
            throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 510, $this->getSourceContext());
        }
        $__internal_compile_59 = twig_to_array($__internal_compile_59);
        $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_59));
        $this->displayBlock("attributes", $context, $blocks);
        $context = $__internal_compile_58;
        echo ">";
        // line 511
        $this->loadTemplate((isset($context["theme"]) || array_key_exists("theme", $context) ? $context["theme"] : (function () { throw new RuntimeError('Variable "theme" does not exist.', 511, $this->source); })()), "@KreyuDataTable/themes/base.html.twig", 511)->displayBlock("action_value", $context);
        // line 512
        echo "</";
        echo twig_escape_filter($this->env, (isset($context["button_tag"]) || array_key_exists("button_tag", $context) ? $context["button_tag"] : (function () { throw new RuntimeError('Variable "button_tag" does not exist.', 512, $this->source); })()), "html", null, true);
        echo ">
    </form>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 516
    public function block_sort_arrow_none($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "sort_arrow_none"));

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 518
    public function block_sort_arrow_asc($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "sort_arrow_asc"));

        echo "↑";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 520
    public function block_sort_arrow_desc($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "sort_arrow_desc"));

        echo "↓";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 522
    public function block_attributes($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "attributes"));

        // line 523
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(((array_key_exists("attr", $context)) ? (_twig_default_filter((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 523, $this->source); })()), [])) : ([])));
        foreach ($context['_seq'] as $context["key"] => $context["value"]) {
            echo twig_escape_filter($this->env, $context["key"], "html", null, true);
            echo "=\"";
            echo twig_escape_filter($this->env, $context["value"], "html", null, true);
            echo "\"";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['key'], $context['value'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    public function getTemplateName()
    {
        return "@KreyuDataTable/themes/base.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  2556 => 523,  2549 => 522,  2536 => 520,  2523 => 518,  2511 => 516,  2500 => 512,  2498 => 511,  2485 => 510,  2482 => 509,  2480 => 508,  2475 => 506,  2469 => 505,  2466 => 504,  2463 => 503,  2460 => 502,  2458 => 501,  2455 => 500,  2452 => 499,  2445 => 498,  2437 => 495,  2427 => 494,  2415 => 493,  2412 => 492,  2409 => 491,  2406 => 490,  2404 => 489,  2401 => 488,  2398 => 487,  2391 => 486,  2383 => 483,  2373 => 482,  2360 => 481,  2353 => 480,  2346 => 477,  2341 => 476,  2334 => 475,  2322 => 473,  2305 => 468,  2302 => 467,  2300 => 466,  2299 => 463,  2298 => 462,  2297 => 460,  2295 => 458,  2288 => 457,  2270 => 453,  2267 => 452,  2265 => 451,  2264 => 448,  2263 => 445,  2258 => 444,  2251 => 443,  2242 => 440,  2239 => 439,  2236 => 438,  2233 => 437,  2231 => 436,  2228 => 435,  2226 => 434,  2223 => 433,  2220 => 432,  2217 => 431,  2214 => 430,  2211 => 429,  2208 => 428,  2201 => 427,  2187 => 423,  2182 => 422,  2175 => 421,  2168 => 418,  2161 => 417,  2151 => 414,  2144 => 413,  2137 => 405,  2120 => 408,  2118 => 407,  2101 => 406,  2099 => 405,  2092 => 404,  2084 => 400,  2075 => 399,  2068 => 398,  2060 => 394,  2051 => 393,  2044 => 392,  2036 => 388,  2027 => 387,  2020 => 386,  2012 => 383,  2010 => 382,  1998 => 381,  1991 => 380,  1984 => 377,  1977 => 376,  1970 => 373,  1963 => 372,  1956 => 369,  1949 => 368,  1941 => 365,  1938 => 363,  1935 => 361,  1933 => 360,  1929 => 359,  1922 => 358,  1911 => 352,  1905 => 350,  1902 => 349,  1895 => 348,  1885 => 343,  1883 => 342,  1872 => 341,  1867 => 340,  1862 => 337,  1856 => 335,  1853 => 334,  1847 => 332,  1841 => 330,  1838 => 329,  1836 => 328,  1834 => 326,  1823 => 325,  1820 => 324,  1817 => 323,  1815 => 322,  1812 => 321,  1810 => 320,  1807 => 319,  1805 => 317,  1803 => 316,  1801 => 315,  1797 => 314,  1794 => 313,  1791 => 312,  1789 => 311,  1786 => 310,  1783 => 309,  1781 => 308,  1778 => 307,  1775 => 306,  1772 => 305,  1770 => 304,  1767 => 303,  1764 => 302,  1757 => 301,  1748 => 296,  1743 => 295,  1736 => 294,  1726 => 290,  1713 => 289,  1706 => 288,  1694 => 285,  1687 => 284,  1677 => 280,  1674 => 279,  1657 => 277,  1640 => 276,  1635 => 275,  1628 => 274,  1618 => 271,  1611 => 270,  1599 => 267,  1592 => 266,  1581 => 262,  1579 => 261,  1573 => 259,  1569 => 257,  1566 => 256,  1563 => 255,  1556 => 254,  1543 => 250,  1531 => 247,  1524 => 246,  1514 => 242,  1501 => 241,  1494 => 240,  1481 => 238,  1469 => 235,  1462 => 234,  1452 => 230,  1439 => 229,  1432 => 228,  1419 => 226,  1407 => 223,  1400 => 222,  1390 => 218,  1377 => 217,  1370 => 216,  1357 => 214,  1347 => 210,  1334 => 209,  1327 => 208,  1317 => 204,  1304 => 203,  1297 => 202,  1284 => 200,  1274 => 196,  1261 => 195,  1254 => 194,  1244 => 190,  1231 => 189,  1224 => 188,  1217 => 184,  1216 => 183,  1215 => 182,  1214 => 181,  1207 => 180,  1199 => 177,  1197 => 176,  1193 => 175,  1186 => 174,  1178 => 170,  1174 => 169,  1167 => 166,  1159 => 165,  1156 => 164,  1150 => 162,  1142 => 161,  1140 => 160,  1126 => 158,  1123 => 157,  1117 => 155,  1108 => 154,  1102 => 152,  1099 => 151,  1082 => 150,  1079 => 147,  1075 => 146,  1068 => 143,  1060 => 142,  1057 => 141,  1051 => 139,  1043 => 138,  1041 => 137,  1034 => 136,  1025 => 133,  1020 => 132,  1013 => 131,  1002 => 127,  999 => 126,  992 => 125,  982 => 120,  975 => 119,  965 => 116,  958 => 115,  948 => 112,  941 => 111,  930 => 107,  924 => 105,  921 => 104,  914 => 103,  902 => 99,  900 => 98,  898 => 97,  893 => 96,  886 => 95,  878 => 92,  872 => 90,  868 => 89,  855 => 88,  848 => 87,  838 => 84,  831 => 83,  809 => 79,  806 => 78,  799 => 77,  767 => 73,  749 => 72,  742 => 71,  731 => 67,  725 => 65,  722 => 64,  715 => 63,  705 => 60,  698 => 59,  688 => 56,  681 => 55,  671 => 52,  664 => 51,  654 => 47,  650 => 46,  637 => 45,  630 => 44,  618 => 42,  608 => 39,  601 => 38,  591 => 35,  584 => 34,  576 => 31,  570 => 29,  568 => 28,  563 => 26,  559 => 25,  555 => 24,  550 => 22,  545 => 21,  538 => 20,  530 => 17,  524 => 15,  522 => 14,  517 => 12,  513 => 11,  510 => 10,  506 => 8,  504 => 7,  499 => 6,  492 => 5,  485 => 522,  483 => 520,  480 => 519,  478 => 518,  475 => 517,  473 => 516,  470 => 515,  468 => 498,  465 => 497,  463 => 486,  460 => 485,  458 => 480,  455 => 479,  453 => 475,  450 => 474,  448 => 473,  445 => 472,  442 => 470,  440 => 457,  437 => 456,  435 => 443,  432 => 442,  430 => 427,  427 => 426,  425 => 421,  422 => 420,  420 => 417,  417 => 416,  415 => 413,  412 => 412,  410 => 404,  407 => 403,  405 => 398,  402 => 397,  400 => 392,  397 => 391,  395 => 386,  392 => 385,  390 => 380,  387 => 379,  385 => 376,  382 => 375,  380 => 372,  377 => 371,  375 => 368,  372 => 367,  370 => 358,  367 => 357,  364 => 355,  362 => 348,  359 => 347,  357 => 301,  354 => 300,  351 => 298,  349 => 294,  346 => 293,  344 => 288,  341 => 287,  339 => 284,  336 => 283,  334 => 274,  331 => 273,  329 => 270,  326 => 269,  324 => 266,  321 => 265,  319 => 254,  316 => 253,  313 => 251,  311 => 250,  308 => 249,  306 => 246,  303 => 245,  301 => 240,  298 => 239,  296 => 238,  293 => 237,  291 => 234,  288 => 233,  286 => 228,  283 => 227,  281 => 226,  278 => 225,  276 => 222,  273 => 221,  271 => 216,  268 => 215,  266 => 214,  263 => 213,  261 => 208,  258 => 207,  256 => 202,  253 => 201,  251 => 200,  248 => 199,  246 => 194,  243 => 193,  241 => 188,  238 => 187,  236 => 180,  233 => 179,  231 => 174,  228 => 173,  226 => 136,  223 => 135,  221 => 131,  218 => 130,  216 => 125,  213 => 124,  210 => 122,  208 => 119,  205 => 118,  203 => 115,  200 => 114,  198 => 111,  195 => 110,  193 => 103,  190 => 102,  188 => 95,  185 => 94,  183 => 87,  180 => 86,  178 => 83,  175 => 82,  173 => 77,  170 => 76,  168 => 71,  165 => 70,  163 => 63,  160 => 62,  158 => 59,  155 => 58,  153 => 55,  150 => 54,  148 => 51,  145 => 50,  143 => 44,  140 => 43,  138 => 42,  135 => 41,  133 => 38,  130 => 37,  128 => 34,  125 => 33,  123 => 20,  120 => 19,  118 => 5,  115 => 4,  112 => 2,);
    }

    public function getSourceContext()
    {
        return new Source("{% trans_default_domain 'KreyuDataTable' %}

{# Base HTML Theme #}

{% block kreyu_data_table %}
    <turbo-frame id=\"kreyu_data_table_{{ name }}\"
        {% if has_batch_actions %}
            data-controller=\"kreyu--data-table-bundle--batch\"
        {% endif %}
    >
        {{ block('action_bar') }}
        {{ block('table') }}

        {% if pagination_enabled %}
            {{ data_table_pagination(pagination) }}
        {% endif %}
    </turbo-frame>
{% endblock %}

{% block kreyu_data_table_form_aware %}
    <turbo-frame id=\"kreyu_data_table_{{ name }}\">
        {{ block('action_bar') }}

        {{ form_start(form, form_variables) }}
            {{ block('table') }}
        {{ form_end(form, { render_rest: false }) }}

        {% if pagination_enabled %}
            {{ data_table_pagination(pagination) }}
        {% endif %}
    </turbo-frame>
{% endblock %}

{% block kreyu_data_table_table %}
    {{ block('table') }}
{% endblock %}

{% block kreyu_data_table_action_bar %}
    {{ block('action_bar') }}
{% endblock %}

{% block action_bar %}{% endblock %}

{% block table %}
    <table {% with { attr: table_attr|default({}) } %}{{- block('attributes') -}}{% endwith %}>
        {{ block('table_head') }}
        {{ block('table_body') }}
    </table>
{% endblock %}

{% block table_head %}
    <thead>{{ block('table_head_row') }}</thead>
{% endblock %}

{% block table_head_row %}
    {{ data_table_header_row(header_row) }}
{% endblock %}

{% block table_body %}
    <tbody>{{ block('table_body_row') }}</tbody>
{% endblock %}

{% block table_body_row %}
    {% if value_rows|length > 0 %}
        {{ block('table_body_row_results') }}
    {% else %}
        {{ block('table_body_row_no_results') }}
    {% endif %}
{% endblock %}

{% block table_body_row_results %}
    {% for value_row in value_rows %}
        <tr {% with { attr: value_row.vars.attr } %}{{ block('attributes') }}{% endwith %}>{{ data_table_value_row(value_row) }}</tr>
    {% endfor %}
{% endblock %}

{% block table_body_row_no_results %}
    <tr>
        <td colspan=\"{{ column_count }}\" {% with { attr: table_body_row_no_results_attr|default({}) } %}{{- block('attributes') -}}{% endwith %}>{{ 'No results'|trans({}, 'KreyuDataTable') }}</td>
    </tr>
{% endblock %}

{% block pagination %}
    {{ data_table_pagination(pagination) }}
{% endblock %}

{% block kreyu_data_table_header_row %}
    <tr {% with { attr: row.vars.attr } %}{{ block('attributes') }}{% endwith %}>
        {% for column_header in row %}
            {{- data_table_column_header(column_header) -}}
        {% endfor %}
    </tr>
{% endblock %}

{% block kreyu_data_table_value_row %}
    {% for column_value in row %}
        <td>
            {{- data_table_column_value(column_value) -}}
        </td>
    {% endfor %}
{% endblock %}

{% block kreyu_data_table_column_label %}
    {% if translation_domain is not same as false %}
        <span>{{- label|trans(translation_parameters, translation_domain) -}}</span>
    {% else %}
        <span>{{- label -}}</span>
    {% endif %}
{% endblock %}

{% block kreyu_data_table_column_header %}
    {{ block(block_name, theme) }}
{% endblock %}

{% block kreyu_data_table_column_value %}
    {{ block(block_name, theme) }}
{% endblock %}

{% block kreyu_data_table_action %}
    {% if visible %}{{- block(block_name, theme) -}}{% endif %}
{% endblock %}

{# Pagination #}

{% block kreyu_data_table_pagination %}
    {% if page_count > 1 %}
        {{ block('pagination_widget', theme) }}
    {% endif %}
{% endblock %}

{% block pagination_widget %}
    {{ block('pagination_counters', theme) }}
    {{ block('pagination_controls', theme) }}
{% endblock %}

{% block pagination_controls %}
    {%- if has_previous_page -%}
        {% with { path: path(app.request.get('_route'), app.request.attributes.get('_route_params')|merge(app.request.query.all)|merge({ (page_parameter_name): 1 })) } %}
            {{ block('pagination_first', theme) }}
        {% endwith %}

        {% with { path: path(app.request.get('_route'), app.request.attributes.get('_route_params')|merge(app.request.query.all)|merge({ (page_parameter_name): current_page_number - 1 })) } %}
            {{ block('pagination_previous', theme) }}
        {% endwith %}
    {%- else -%}
        {{ block('pagination_first_disabled', theme) }}
        {{ block('pagination_previous_disabled', theme) }}
    {%- endif -%}

    {% for page_number in range(first_visible_page_number, last_visible_page_number) %}
        {% if page_number == current_page_number %}
            {{ block('pagination_page_active', theme) }}
        {% else %}
            {% with { path: path(app.request.get('_route'), app.request.attributes.get('_route_params')|merge(app.request.query.all)|merge({ (page_parameter_name): page_number })) } %}
                {{ block('pagination_page', theme) }}
            {% endwith %}
        {% endif %}
    {% endfor %}

    {%- if has_next_page -%}
        {% with { path: path(app.request.get('_route'), app.request.attributes.get('_route_params')|merge(app.request.query.all)|merge({ (page_parameter_name): current_page_number + 1 })) } %}
            {{ block('pagination_next', theme) }}
        {% endwith %}

        {% with { path: path(app.request.get('_route'), app.request.attributes.get('_route_params')|merge(app.request.query.all)|merge({ (page_parameter_name): page_count })) } %}
            {{ block('pagination_last', theme) }}
        {% endwith %}
    {%- else -%}
        {{ block('pagination_next_disabled', theme) }}
        {{ block('pagination_last_disabled', theme) }}
    {%- endif -%}
{% endblock %}

{% block pagination_counters %}
    <span {{- block('attributes') -}}>
        {{- block('pagination_counters_message', theme) -}}
    </span>
{% endblock %}

{% block pagination_counters_message %}
    {{- 'Showing %current_page_first_item_index% - %current_page_last_item_index% of %total_item_count%'|trans({
        '%current_page_first_item_index%': current_page_first_item_index,
        '%current_page_last_item_index%': current_page_last_item_index,
        '%total_item_count%': total_item_count
    }, 'KreyuDataTable') -}}
{% endblock %}

{% block pagination_page %}
    <a {% with { attr: attr|default({})|merge({ href: path, 'data-turbo-action': 'advance' }) } %}{{ block('attributes') }}{% endwith %}>
        {{ block('pagination_page_message', theme) }}
    </a>
{% endblock %}

{% block pagination_page_active %}
    <span {% with { attr: attr|default({})|merge({ 'data-turbo-action': 'advance' }) } %}{{ block('attributes') }}{% endwith %}>
        {{ block('pagination_page_message', theme) }}
    </span>
{% endblock %}

{% block pagination_page_message page_number %}

{% block pagination_first %}
    <a {% with { attr: attr|default({})|merge({ href: path, 'data-turbo-action': 'advance' }) } %}{{ block('attributes') }}{% endwith %}>
        {{ block('pagination_first_message', theme) }}
    </a>
{% endblock %}

{% block pagination_first_disabled %}
    <span {% with { attr: attr|default({})|merge({ 'data-turbo-action': 'advance' }) } %}{{ block('attributes') }}{% endwith %}>
        {{ block('pagination_first_message', theme) }}
    </span>
{% endblock %}

{% block pagination_first_message '«' %}

{% block pagination_previous %}
    <a {% with { attr: attr|default({})|merge({ href: path, 'data-turbo-action': 'advance' }) } %}{{ block('attributes') }}{% endwith %}>
        {{ block('pagination_previous_message', theme) }}
    </a>
{% endblock %}

{% block pagination_previous_disabled %}
    <span {{ block('attributes') }}>{{ block('pagination_previous_message', theme) }}</span>
{% endblock %}

{% block pagination_previous_message '‹' %}

{% block pagination_last %}
    <a {% with { attr: attr|default({})|merge({ href: path, 'data-turbo-action': 'advance' }) } %}{{ block('attributes') }}{% endwith %}>
        {{ block('pagination_last_message', theme) }}
    </a>
{% endblock %}

{% block pagination_last_disabled %}
    <span {{ block('attributes') }}>{{ block('pagination_last_message', theme) }}</span>
{% endblock %}

{% block pagination_last_message '»' %}

{% block pagination_next %}
    <a {% with { attr: attr|default({})|merge({ href: path, 'data-turbo-action': 'advance' }) } %}{{ block('attributes') }}{% endwith %}>
        {{ block('pagination_next_message', theme) }}
    </a>
{% endblock %}

{% block pagination_next_disabled %}
    <span {{ block('attributes') }}>{{ block('pagination_next_message', theme) }}</span>
{% endblock %}

{% block pagination_next_message '›' %}

{# Filtration #}

{% block kreyu_data_table_filters_form %}
    {% form_theme form with form_themes|default([_self]) %}

    {{ form_start(form, { attr: { 'data-turbo-action': 'advance', 'hidden': 'hidden' } }) }}
        {# This form should be empty - all its inputs should be on the outside, referenced using the \"form\" attribute #}
    {{ form_end(form, { render_rest: false }) }}

    {% if form.count > 0 %}
        {{ block('filtration_widget', theme) }}
    {% endif %}
{% endblock %}

{% block filtration_widget %}
    <div {{ block('attributes') }}>{{ block('filtration_form', theme) }}</div>
{% endblock %}

{% block filtration_form %}
    {{ block('filtration_form_content', theme) }}
{% endblock %}

{% block filtration_form_content %}
    <div {{ block('attributes') }}>
        {% for child in form %}
            {{ block('filtration_form_row', theme) }}
        {% endfor %}

        {{ block('filtration_form_submit', theme) }}
    </div>
{% endblock %}

{% block filtration_form_row %}
    <div {{ block('attributes') }}>{{ form_row(child) }}</div>
{% endblock %}

{% block filtration_form_submit %}
    <button {% with { attr: attr|default({})|merge({ form: form.vars.id, 'data-turbo-action': 'advance' }) } %}{{ block('attributes') }}{% endwith %}>
        {{ 'Filter'|trans({}, 'KreyuDataTable') }}
    </button>
{% endblock %}

{% block kreyu_data_table_date_range_widget %}
    {{ form_widget(form.from) }}
    {{ form_widget(form.to) }}
{% endblock %}

{# Column type header templates #}

{% block column_header %}
    {% set label_attr = label_attr|default({}) %}

    {% if data_table.vars.sorting_enabled and sortable %}
        {% set current_sort_field = sorting_field_data.name|default(null) %}
        {% set current_sort_direction = sorting_field_data.direction|default(null) %}

        {% set active_attr = active_attr|default({}) %}
        {% set inactive_attr = inactive_attr|default({}) %}

        {% set attr = attr|default({}) %}
        {% set attr = attr|merge(sorted ? active_attr : inactive_attr) %}

        <th {{ block('attributes') }}>
            {% set query_params = app.request.query.all %}
            {% set query_params = query_params|merge({ (sort_parameter_name): {
                (name): sort_direction|lower == 'desc' ? 'asc' : 'desc'
            } }) %}

            {% set query_params = app.request.attributes.get('_route_params')|merge(query_params) %}

            {% set label_attr = { href: path(app.request.get('_route'), query_params) }|merge(label_attr) %}
            {% set label_attr = { 'data-turbo-action': 'advance' }|merge(label_attr) %}

            <a {% with { attr: label_attr } %}{{- block('attributes') -}}{% endwith %}>
                {{- block('column_header_label', theme, _context) -}}

                {% if sorted %}
                    {% if sort_direction == 'asc' %}
                        {{ block('sort_arrow_asc', theme, _context) }}
                    {% else %}
                        {{ block('sort_arrow_desc', theme, _context) }}
                    {% endif %}
                {% else %}
                    {{ block('sort_arrow_none', theme, _context) }}
                {% endif %}
            </a>
        </th>
    {% else %}
        <th {{ block('attributes') }}>
            <span {% with { attr: label_attr } %}{{- block('attributes') -}}{% endwith %}>
                {{- block('column_header_label', theme, _context) -}}
            </span>
        </th>
    {% endif %}
{% endblock %}

{% block column_header_label %}
    {% if translation_domain is not same as false %}
        <span>{{- label|trans(translation_parameters, translation_domain) -}}</span>
    {% else %}
        <span>{{- label -}}</span>
    {% endif %}
{% endblock %}

{# Column type value templates #}

{% block column_value %}
    <span {{- block('attributes') -}}>
        {%- if translation_domain is not same as false -%}
            {{- value|trans({}, translation_domain) -}}
        {%- else -%}
            {{- value -}}
        {%- endif -%}
    </span>
{% endblock %}

{% block column_text_value %}
    {{- block('column_value') -}}
{% endblock %}

{% block column_number_value %}
    {{- block('column_text_value') -}}
{% endblock %}

{% block column_money_value %}
    {{- block('column_text_value') -}}
{% endblock %}

{% block column_link_value %}
    <a {% with { attr: { href, target }|merge(attr) } %}{{- block('attributes') -}}{% endwith %}>
        {{- block('column_text_value') -}}
    </a>
{% endblock %}

{% block column_date_time_value %}
    {% with { value: value ? value|date(format, timezone) : value } %}
        {{- block('column_text_value') -}}
    {% endwith %}
{% endblock %}

{% block column_date_period_value %}
    {% with { value: value ? value.start|date(format, timezone) ~ separator ~ value.end|date(format, timezone) : value } %}
        {{- block('column_text_value') -}}
    {% endwith %}
{% endblock %}

{% block column_boolean_value %}
    {% with { value: value ? label_true : label_false } %}
        {{- block('column_text_value') -}}
    {% endwith %}
{% endblock %}

{% block column_collection_value %}
    {%- apply spaceless %}
        {%- for child in children %}
            {{- data_table_column_value(child) -}}
            {%- if not loop.last %}{{ block('column_collection_separator', theme) }}{% endif -%}
        {% endfor -%}
    {% endapply -%}
{% endblock %}

{% block column_collection_separator %}
    <span>{{ separator }}</span>
{% endblock %}

{% block column_template_value %}
    {{- include(template_path, template_vars) -}}
{% endblock %}

{% block column_actions_value %}
    {% for action in actions %}
        {{ data_table_action(action) }}
    {% endfor %}
{% endblock %}

{% block column_form_value %}
    {% if form_child_path is not same as false %}
        {% set form = form[row_index][form_child_path].createView() %}
    {% else %}
        {% set form = form[row_index].createView() %}
    {% endif %}

    {% set form_themes = form_themes|default(null) %}

    {% if form_themes is not null %}
        {% form_theme form with form_themes %}
    {% endif %}

    {{ form_widget(form) }}
{% endblock %}

{% block column_checkbox_header %}
    <th {{ block('attributes') }}>
        {% set input_attr = {
            'type': 'checkbox',
            'aria-label': 'Select all checkbox',
            'data-identifier-name': identifier_name,
            'data-kreyu--data-table-bundle--batch-target': 'selectAllCheckbox',
            'data-action': 'input->kreyu--data-table-bundle--batch#selectAll'
        }|merge(input_attr|default({})) %}

        <input {% with { attr: input_attr } %}{{ block('attributes') }}{% endwith %}>
    </th>
{% endblock %}

{% block column_checkbox_value %}
    {% set input_attr = {
        'type': 'checkbox',
        'value': value,
        'aria-label': 'Select all checkbox',
        'data-index': row.index,
        'data-identifier-name': identifier_name,
        'data-kreyu--data-table-bundle--batch-target': 'selectRowCheckbox',
        'data-action': 'input->kreyu--data-table-bundle--batch#selectRow'
    }|merge(input_attr|default({})) %}

    <input {% with { attr: input_attr } %}{{ block('attributes') }}{% endwith %}>
{% endblock %}

{# Action type templates #}

{% block action_value_icon %}{% endblock %}

{% block action_value %}
    {% if icon_attr %}{{ block('action_value_icon', theme, _context) }}{% endif %}
    {{- label|trans(translation_parameters, translation_domain) -}}
{% endblock %}

{% block action_link_value %}
    <a {% with { attr: { href, target }|merge(attr) } %}{{- block('attributes') -}}{% endwith %}>
        {% with { attr: {} } %}{{- block('action_value', theme, _context) -}}{% endwith %}
    </a>
{% endblock %}

{% block action_button_value %}
    {% set attr = { href, target }|merge(attr|default({})) %}

    {% if batch %}
        {% set attr = { 'data-kreyu--data-table-bundle--batch-target': 'identifierHolder' }|merge(attr) %}
    {% endif %}

    <a {% with { attr } %}{{- block('attributes') -}}{% endwith %}>
        {% with { attr: {} } %}{{- block('action_value', theme, _context) -}}{% endwith %}
    </a>
{% endblock %}

{% block action_form_value %}
    {% set attr = { action, method: html_friendly_method }|merge(attr|default({})) %}

    {% if batch %}
        {% set attr = { 'data-kreyu--data-table-bundle--batch-target': 'identifierHolder' }|merge(attr) %}
    {% endif %}

    <form id=\"{{ form_id }}\" {{- block('attributes') -}}>
        <input type=\"hidden\" name=\"_method\" value=\"{{ method }}\"/>

        {% set button_tag = button_tag|default('button') %}

        <{{ button_tag }} {% with { attr: { type: 'submit' }|merge(button_attr) } %}{{- block('attributes') -}}{% endwith %}>
            {{- block('action_value', theme, _context) -}}
        </{{ button_tag }}>
    </form>
{% endblock %}

{% block sort_arrow_none %}{% endblock %}

{% block sort_arrow_asc %}↑{% endblock %}

{% block sort_arrow_desc %}↓{% endblock %}

{%- block attributes %}
    {%- for key, value in attr|default({}) %}{{ key }}=\"{{ value }}\"{% endfor -%}
{% endblock -%}
", "@KreyuDataTable/themes/base.html.twig", "/home/kreyu/projects/personal/data-table-bundle-demo/lib/kreyu/data-table-bundle/src/Resources/views/themes/base.html.twig");
    }
}
