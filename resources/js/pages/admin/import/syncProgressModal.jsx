import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
import { Progress } from '@/components/ui/progress'
import {
    CheckCircle2,
    XCircle,
    AlertTriangle,
    Lock,
    Loader2,
} from 'lucide-react'

export default function SyncProgressModal({
                                              open,
                                              serviceProgress,
                                              chunkProgress,
                                              status,
                                              message,
                                          }) {
    return (
        <Dialog open={open}>
            <DialogContent className="max-w-md" onInteractOutside={(e) => e.preventDefault()} onEscapeKeyDown={(e) => e.preventDefault()}>
                <DialogHeader className="space-y-2">
                    <DialogTitle className="flex items-center gap-2">
                        <Lock className="h-5 w-5 text-gray-600" />
                        Data synchronization in progress
                    </DialogTitle>

                    {status === 'running' && <p className="text-muted-foreground text-xs">System operation in progress</p>}
                </DialogHeader>

                <div className="space-y-6">
                    {/* 🔴 Hard Warning */}
                    {status === 'running' && (
                        <div className="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                            <div className="flex items-start gap-3">
                                <AlertTriangle className="mt-0.5 h-5 w-5" />
                                <div className="space-y-1">
                                    <p className="font-semibold">Do not close or navigate away</p>
                                    <p className="text-xs leading-relaxed">
                                        A critical data synchronization is currently running. Interrupting this process may result in incomplete or
                                        inconsistent data.
                                    </p>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Overall progress */}
                    <div>
                        <div className="mb-1 flex justify-between text-sm">
                            <span className="font-medium">Overall Progress</span>
                            <span className="text-muted-foreground">{serviceProgress}%</span>
                        </div>
                        <Progress value={serviceProgress} className="h-2 bg-blue-100" />
                    </div>

                    {/* Current task progress */}
                    <div>
                        <div className="mb-1 flex justify-between text-sm">
                            <span className="font-medium">Current Operation</span>
                            <span className="text-muted-foreground">{chunkProgress}%</span>
                        </div>
                        <Progress value={chunkProgress} className="h-2 bg-green-100" />
                    </div>

                    {/* Status line */}
                    <div className="flex items-center gap-2 text-sm">
                        {status === 'running' && <Loader2 className="h-5 w-5 animate-spin text-blue-600" />}
                        {status === 'success' && <CheckCircle2 className="h-5 w-5 text-green-600" />}
                        {status === 'failed' && <XCircle className="h-5 w-5 text-red-600" />}

                        <span className="leading-relaxed">{message}</span>
                    </div>

                    {/* Footer notice */}
                    <div className="text-amber-700 border-t pt-3 text-xs">
                        This window is locked until the synchronization completes. Please wait for confirmation before continuing.
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
}
