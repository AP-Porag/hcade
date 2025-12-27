import React, { useState, useEffect } from 'react';
import { Head, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';
import DataTable from '@/components/DataTable';
import AppLayout from '@/layouts/app-layout.js';

const breadcrumbs = [
    {
        title: 'Users',
        href: '/users/create',
    },
];

export default function Index({ users, filters: initialFilters }) {
    const [filters, setFilters] = useState({
        search: initialFilters.search || '',
        status: initialFilters.status || '',
        perPage: initialFilters.perPage || 5,
        page: users.current_page || 1,
    });

    useEffect(() => {
        // Push new filters to URL and reload data
        router.get(route('users.index'), filters, {
            preserveState: true,
            replace: true,
        });
    }, [filters.search, filters.status, filters.perPage, filters.page]);

    const columns = [
        {
            key: 'avatar',
            label: 'Avatar',
            render: (row) => <img src={row.avatar || '/images/user_default.png'} alt="avatar" className="h-10 w-10 rounded-full object-cover" />,
        },
        { key: 'name', label: 'Name' },
        {
            key: 'email',
            label: 'Email',
            render: (row) => <span className="block w-48 truncate">{row.email}</span>,
        },
        {
            key: 'user_type',
            label: 'Role',
            render: (row) => <span className="block w-48 truncate capitalize">{row.user_type}</span>,
        },
        {
            key: 'status',
            label: 'Status',
            render: (row) => {
                const statusStyles = {
                    1: 'bg-green-100 text-green-800',
                    0: 'bg-red-100 text-red-800',
                };
                return (
                    <span className={`rounded px-2 py-1 text-xs font-medium ${statusStyles[row.status] || 'bg-gray-100 text-gray-800'}`}>
                        {row.status === 1 ? 'Active' : 'Inactive'}
                    </span>
                );
            },
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Users" />
            <div className="p-4">
                <div className="flex justify-between items-center my-4">
                    <h1 className="text-2xl font-bold">Users</h1>
                    <Button
                        onClick={() => router.visit(route('users.create'))}
                        className="bg-black text-white hover:bg-gray-800 cursor-pointer"
                    >
                        <Plus className="mr-2" /> Create User
                    </Button>
                </div>
                <DataTable
                    data={users.data}
                    columns={columns}
                    meta={{
                        from: users.from,
                        to: users.to,
                        total: users.total,
                        current_page: users.current_page,
                        last_page: users.last_page,
                    }}
                    baseRoute="users"
                    filters={filters}
                    onFilterChange={setFilters}
                />
            </div>
        </AppLayout>
    );
}
