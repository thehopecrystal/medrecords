import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export function useFlash() {
    const { flash } = usePage().props;
    const [toast, setToast] = useState(null);

    useEffect(() => {
        if (flash?.success) setToast({ type: 'success', message: flash.success });
        else if (flash?.error) setToast({ type: 'error', message: flash.error });
        else setToast(null);
    }, [flash?.success, flash?.error]);

    return { toast, dismiss: () => setToast(null) };
}
