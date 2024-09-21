import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: 'DataTableBundle',
  description: 'Streamlines creation process of the data tables',
  srcDir: './src',
  head: [
    ['link', { rel: 'icon', type: 'image/png', href: '/logo.png' }],
  ],
  themeConfig: {
    logo: '/logo.png',
    externalLinkIcon: true,
    outline: 'deep',

    search: {
      provider: 'local',
    },

    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Documentation', link: '/docs/introduction', activeMatch: '/docs/' },
      { text: 'Reference', link: '/reference/types/data-table', activeMatch: '/reference/' },
    ],

    sidebar: {
      '/docs/': [
        {
          text: 'Getting started',
          items: [
            { text: 'Introduction', link: '/docs/introduction' },
            { text: 'Installation', link: '/docs/installation' },
            { text: 'Usage', link: '/docs/usage' },
          ]
        },
        {
          text: 'Components',
          items: [
            { text: 'Columns', link: '/docs/components/columns' },
            { text: 'Filters', link: '/docs/components/filters' },
            { text: 'Actions', link: '/docs/components/actions' },
            { text: 'Exporters', link: '/docs/components/exporters' },
          ]
        },
        {
          text: 'Features',
          items: [
            { text: 'Sorting', link: '/docs/features/sorting' },
            { text: 'Filtering', link: '/docs/features/filtering' },
            { text: 'Exporting', link: '/docs/features/exporting' },
            { text: 'Pagination', link: '/docs/features/pagination' },
            { text: 'Personalization', link: '/docs/features/personalization' },
            { text: 'Persistence', link: '/docs/features/persistence' },
            { text: 'Theming', link: '/docs/features/theming' },
            { text: 'Asynchronicity', link: '/docs/features/asynchronicity' },
            { text: 'Profiler', link: '/docs/features/profiler' },
            { text: 'Extensibility', link: '/docs/features/extensibility' },
          ]
        },
        {
          text: 'Integrations',
          items: [
            {
              text: 'Doctrine ORM',
              collapsed: true,
              items: [
                { text: 'Expression transformers', link: '/docs/integrations/doctrine-orm/expression-transformers' },
                { text: 'Events', link: '/docs/integrations/doctrine-orm/events' }
              ],
            },
          ]
        },
        { text: 'Troubleshooting', link: '/docs/troubleshooting' },
        { text: 'Contributing', link: '/docs/contributing' },
      ],
      '/reference/': [
        {
          text: 'Types',
          items: [
            {
              text: 'DataTable',
              link: '/reference/types/data-table'
            },
            {
              text: 'Column',
              link: '/reference/types/column',
              collapsed: true,
              items: [
                { text: 'Text', link: '/reference/types/column/text' },
                { text: 'Number', link: '/reference/types/column/number' },
                { text: 'Money', link: '/reference/types/column/money' },
                { text: 'Boolean', link: '/reference/types/column/boolean' },
                { text: 'Link', link: '/reference/types/column/link' },
                { text: 'Date', link: '/reference/types/column/date' },
                { text: 'DateTime', link: '/reference/types/column/date-time' },
                { text: 'DatePeriod', link: '/reference/types/column/date-period' },
                { text: 'Collection', link: '/reference/types/column/collection' },
                { text: 'Enum', link: '/reference/types/column/enum' },
                { text: 'Template', link: '/reference/types/column/template' },
                { text: 'Actions', link: '/reference/types/column/actions' },
                { text: 'Checkbox', link: '/reference/types/column/checkbox' },
                { text: 'Column', link: '/reference/types/column/column' },
              ]
            },
            {
              text: 'Filter',
              link: '/reference/types/filter',
              collapsed: true,
              items: [
                {
                  text: 'Doctrine ORM',
                  collapsed: false,
                  items: [
                    { text: 'String', link: '/reference/types/filter/doctrine-orm/string' },
                    { text: 'Numeric', link: '/reference/types/filter/doctrine-orm/numeric' },
                    { text: 'Boolean', link: '/reference/types/filter/doctrine-orm/boolean' },
                    { text: 'Date', link: '/reference/types/filter/doctrine-orm/date' },
                    { text: 'DateTime', link: '/reference/types/filter/doctrine-orm/date-time' },
                    { text: 'DateRange', link: '/reference/types/filter/doctrine-orm/date-range' },
                    { text: 'Entity', link: '/reference/types/filter/doctrine-orm/entity' },
                    { text: 'DoctrineOrm', link: '/reference/types/filter/doctrine-orm/doctrine-orm' },
                  ],
                },
                { text: 'Callback', link: '/reference/types/filter/callback' },
                { text: 'Search', link: '/reference/types/filter/search' },
                { text: 'Filter', link: '/reference/types/filter/filter' },
              ],
            },
            {
              text: 'Action',
              link: '/reference/types/action',
              collapsed: true,
              items: [
                { text: 'Link', link: '/reference/types/action/link' },
                { text: 'Button', link: '/reference/types/action/button' },
                { text: 'Form', link: '/reference/types/action/form' },
                { text: 'Action', link: '/reference/types/action/action' },
              ],
            },
            {
              text: 'Exporter',
              link: '/reference/types/exporter',
              collapsed: true,
              items: [
                {
                  text: 'PhpSpreadsheet',
                  collapsed: false,
                  items: [
                    { text: 'Csv', link: '/reference/types/exporter/php-spreadsheet/csv' },
                    { text: 'Xls', link: '/reference/types/exporter/php-spreadsheet/xls' },
                    { text: 'Xlsx', link: '/reference/types/exporter/php-spreadsheet/xlsx' },
                    { text: 'Ods', link: '/reference/types/exporter/php-spreadsheet/ods' },
                    { text: 'Pdf', link: '/reference/types/exporter/php-spreadsheet/pdf' },
                    { text: 'Html', link: '/reference/types/exporter/php-spreadsheet/html' },
                  ],
                },
                {
                  text: 'OpenSpout',
                  collapsed: false,
                  items: [
                    { text: 'Csv', link: '/reference/types/exporter/open-spout/csv' },
                    { text: 'Xlsx', link: '/reference/types/exporter/open-spout/xlsx' },
                    { text: 'Ods', link: '/reference/types/exporter/open-spout/ods' },
                  ],
                },
                { text: 'Callback', link: '/reference/types/exporter/callback' },
                { text: 'Exporter', link: '/reference/types/exporter/exporter' },
              ],
            },
          ]
        },
        { text: 'Configuration', link: '/reference/configuration' },
        { text: 'Twig', link: '/reference/twig' },
      ],
    },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/kreyu/data-table-bundle' }
    ]
  }
})
