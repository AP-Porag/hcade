import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { router, usePage } from '@inertiajs/react';
import axios from 'axios';
import { AlertTriangle, Database, Loader2 } from 'lucide-react';
import { useEffect, useState } from 'react';
import SyncProgressModal from './syncProgressModal.jsx';

export default function DomainSyncCard({ availableYears, defaultYear }) {
    const { props } = usePage();

    const sync_log_id = props.sync_log_id ?? null;

    const [taxYear, setTaxYear] = useState(String(defaultYear));
    const [modalOpen, setModalOpen] = useState(!!sync_log_id);
    const [syncLogId, setSyncLogId] = useState(sync_log_id);
    const [serviceProgress, setServiceProgress] = useState(0);
    const [chunkProgress, setChunkProgress] = useState(0);
    const [message, setMessage] = useState('');
    const [status, setStatus] = useState(sync_log_id ? 'running' : 'idle');


    const startSync = async () => {

        setModalOpen(true);
        setServiceProgress(0);
        setChunkProgress(0);
        setMessage('Starting…');
        setStatus('running');

        const { data } = await axios.post('/data-import/sync-domain', {
            tax_year: taxYear,
        });

        setSyncLogId(data.sync_log_id);
    };

    useEffect(() => {
        if (!sync_log_id) return;
        const init = async () => {
            const { data } = await axios.get(`/data-import/sync-status/${syncLogId}`);

            if (data?.id) {
                setSyncLogId(data.id);
                setServiceProgress(data.service_progress ?? 0);
                setChunkProgress(data.chunk_progress ?? 0);
                setStatus(data.status ?? '');
                setMessage(data.message ?? '');
                setModalOpen(true);
            }
        };

        init();
    }, []);


    useEffect(() => {
        if (!syncLogId) return;

        const timer = setInterval(async () => {
            console.log('running after every second');
            const { data } = await axios.get(
                `/data-import/sync-status/${syncLogId}`
            );

            setServiceProgress(data.service_progress ?? 0);
            setChunkProgress(data.chunk_progress ?? 0);
            setMessage(data.message ?? '');

            if (data.status === 'success') {
                setStatus('success');
                setTimeout(() => setModalOpen(false), 3000);
                clearInterval(timer);
            }

            if (data.status === 'failed') {
                setStatus('error');
                setMessage('Failed to start synchronization.');
                setTimeout(() => setModalOpen(false), 3000);
                clearInterval(timer);
            }
        }, 5000);

        return () => clearInterval(timer);
    }, [syncLogId]);

    return (
        <>
            {/*<Card className="max-w-2xl">*/}
            {/*    <CardHeader>*/}
            {/*        <CardTitle className="flex items-center gap-2">*/}
            {/*            <Database className="h-5 w-5" />*/}
            {/*            Domain Data Synchronization*/}
            {/*        </CardTitle>*/}
            {/*        <CardDescription>Convert RAW HCAD data into clean domain tables.</CardDescription>*/}
            {/*    </CardHeader>*/}

            {/*    <CardContent className="space-y-6">*/}
            {/*        <Select value={taxYear} onValueChange={setTaxYear}>*/}
            {/*            <SelectTrigger>*/}
            {/*                <SelectValue placeholder="Select tax year" />*/}
            {/*            </SelectTrigger>*/}
            {/*            <SelectContent>*/}
            {/*                {availableYears.map((y) => (*/}
            {/*                    <SelectItem key={y} value={String(y)}>*/}
            {/*                        {y}*/}
            {/*                    </SelectItem>*/}
            {/*                ))}*/}
            {/*            </SelectContent>*/}
            {/*        </Select>*/}

            {/*        <div className="flex gap-3 rounded-md border bg-yellow-50 p-4 text-sm">*/}
            {/*            <AlertTriangle className="h-5 w-5" />*/}
            {/*            Do not refresh while syncing.*/}
            {/*        </div>*/}

            {/*        <Button onClick={startSync} disabled={!taxYear}>*/}
            {/*            /!*<Loader2 className="mr-2 h-4 w-4 animate-spin" />*!/*/}
            {/*            Start Synchronization*/}
            {/*        </Button>*/}
            {/*    </CardContent>*/}
            {/*</Card>*/}

            <Card className="max-w-2xl">
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Database className="h-5 w-5" />
                        Domain Data Synchronization
                    </CardTitle>
                    <CardDescription>
                        Build & update clean, indexed domain tables from RAW HCAD data. Used by search, market insights, dashboards, and property views.
                    </CardDescription>
                </CardHeader>

                <CardContent className="space-y-6">
                    <div className="space-y-2">
                        <label className="text-sm font-medium">Tax Year</label>
                        <Select value={taxYear} onValueChange={setTaxYear}>
                            <SelectTrigger>
                                <SelectValue placeholder="Select tax year" />
                            </SelectTrigger>
                            <SelectContent>
                                {availableYears.map((year) => (
                                    <SelectItem key={year} value={String(year)}>
                                        {year}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>

                    <div className="flex gap-3 rounded-md border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                        <AlertTriangle className="mt-0.5 h-5 w-5" />
                        <div>
                            <p className="font-medium">Important</p>
                            <p>Do not refresh or close this page while synchronization is running.</p>
                        </div>
                    </div>

                    <div className="flex justify-end">
                        <Button onClick={startSync} disabled={!taxYear || !availableYears} className="gap-2 cursor-pointer">
                            Start Synchronization
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <SyncProgressModal open={modalOpen} serviceProgress={serviceProgress} chunkProgress={chunkProgress} status={status} message={message} />
        </>
    );
}
