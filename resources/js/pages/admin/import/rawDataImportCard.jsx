import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { UploadCloud } from 'lucide-react';

export default function RawDataImportCard() {
    return (
        <Card className="max-w-2xl opacity-70">
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    <UploadCloud className="h-5 w-5" />
                    RAW Data Import (Coming Soon)
                </CardTitle>
                <CardDescription>
                    Upload official HCAD TXT files to update RAW datasets. This feature will support validation, previews, and resumable imports.
                </CardDescription>
            </CardHeader>

            <CardContent className="space-y-4">
                <p className="text-muted-foreground text-sm">
                    This section will allow administrators to upload and manage large HCAD data files in a controlled and auditable way.
                </p>

                <Button disabled variant="secondary">
                    Upload TXT Files
                </Button>
            </CardContent>
        </Card>
    );
}
