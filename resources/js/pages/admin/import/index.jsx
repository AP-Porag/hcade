import { Head } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout'
import DomainSyncCard from './DomainSyncCard'
import RawDataImportCard from './rawDataImportCard.jsx'

const breadcrumbs = [
    { title: 'Administration', href: '/admin' },
    { title: 'Data Import', href: '/data-import' },
]

export default function Index({ availableYears, defaultYear }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Data Import" />

            <div className="p-6 space-y-8">
                <div>
                    <h1 className="text-2xl font-bold">Data Import & Synchronization</h1>
                    <p className="text-muted-foreground">
                        Manage RAW data ingestion and domain synchronization.
                    </p>
                </div>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <DomainSyncCard
                        availableYears={availableYears}
                        defaultYear={defaultYear}
                    />

                    <RawDataImportCard />
                </div>
            </div>
        </AppLayout>
    )
}
