import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useForm } from '@inertiajs/react';
import { AlertTriangle, Database, Loader2 } from 'lucide-react';
import { useState } from 'react';
import SyncProgressModal from './SyncProgressModal';

export default function DomainSyncCard(props) {
    // ✅ SAFE normalization
    const normalizedYears = Array.isArray(props.availableYears) ? props.availableYears : props.availableYears ? [props.availableYears] : [];

    const defaultYear = props.defaultYear ?? '';

    const [taxYear, setTaxYear] = useState(String(defaultYear));
    const [modalOpen, setModalOpen] = useState(false);

    // ✅ FIXED: proper useState
    const [status, setStatus] = useState('running'); // 'running' | 'success' | 'error'

    const [message, setMessage] = useState('Preparing synchronization…');
    const [progress, setProgress] = useState(0);

    const { post, processing } = useForm({ tax_year: taxYear });

    const startSync = () => {
        setModalOpen(true);
        setStatus('running');
        setProgress(10);
        setMessage('Initializing domain synchronization…');

        post('/data-import/sync-domain', {
            preserveScroll: true,

            onSuccess: () => {
                setProgress(100);
                setStatus('success');
                setMessage('Domain data synchronized successfully.');

                setTimeout(() => {
                    setModalOpen(false);
                }, 2000);
            },

            onError: () => {
                setStatus('error');
                setMessage('Synchronization failed. Please check logs and try again.');
            },
        });
    };

    return (
        <>
            <Card className="max-w-2xl">
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Database className="h-5 w-5" />
                        Domain Data Synchronization
                    </CardTitle>
                    <CardDescription>
                        Build clean, indexed domain tables from RAW HCAD data. Used by search, dashboards, and property views.
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
                                {normalizedYears.map((year) => (
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
                        <Button onClick={startSync} disabled={processing} className="gap-2">
                            {processing && <Loader2 className="h-4 w-4 animate-spin" />}
                            Start Synchronization
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <SyncProgressModal open={modalOpen} progress={progress} status={status} message={message} />
        </>
    );
}
