import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Progress } from '@/components/ui/progress';
import { CheckCircle2, XCircle } from 'lucide-react';

export default function SyncProgressModal({ open, progress, status, message }) {
    return (
        <Dialog open={open}>
            <DialogContent className="max-w-md" onInteractOutside={(e) => e.preventDefault()} onEscapeKeyDown={(e) => e.preventDefault()}>
                <DialogHeader>
                    <DialogTitle>Domain Synchronization</DialogTitle>
                </DialogHeader>

                <div className="space-y-4">
                    <Progress value={progress} />

                    <div className="flex items-center gap-2 text-sm">
                        {status === 'success' && <CheckCircle2 className="h-5 w-5 text-green-600" />}
                        {status === 'error' && <XCircle className="h-5 w-5 text-red-600" />}
                        <span>{message}</span>
                    </div>

                    <p className="text-muted-foreground text-xs">Please do not refresh this page while the process is running.</p>
                </div>
            </DialogContent>
        </Dialog>
    );
}
