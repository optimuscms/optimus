import Dashboard from '../../../components/ui/Dashboard';

import PageIndex from '../views/Index';
import PageCreate from '../views/Create';
import PageEdit from '../views/Edit';

let routes = [
    {
        path: '/pages',
        component: Dashboard,
        meta: { section: 'pages' },

        children: [
            {
                path: '',
                name: 'pages.index',
                component: PageIndex,
            },
            {
                path: 'create',
                name: 'pages.create',
                component: PageCreate,
            },
            {
                path: ':id/edit',
                name: 'pages.edit',
                component: PageEdit,
            },
        ],
    },
];

export default routes;