import * as React from 'react';
import * as ToastPrimitive from '@radix-ui/react-toast';
import { X, CheckCircle, AlertCircle } from 'lucide-react';
import { cn } from '@/lib/utils';

const ToastProvider = ToastPrimitive.Provider;
const ToastViewport = React.forwardRef(({ className, ...props }, ref) => (
    <ToastPrimitive.Viewport
        ref={ref}
        className={cn('fixed bottom-4 right-4 z-[100] flex max-h-screen w-full max-w-sm flex-col gap-2', className)}
        {...props}
    />
));
ToastViewport.displayName = ToastPrimitive.Viewport.displayName;

const Toast = React.forwardRef(({ className, variant = 'default', ...props }, ref) => (
    <ToastPrimitive.Root
        ref={ref}
        className={cn(
            'group pointer-events-auto relative flex w-full items-start gap-3 rounded-[var(--radius)] border p-4 shadow-lg transition-all',
            variant === 'success' && 'border-[var(--color-green)] bg-[var(--color-green-lt)] text-[var(--color-green)]',
            variant === 'error'   && 'border-[var(--color-red)] bg-[var(--color-red-lt)] text-[var(--color-red)]',
            variant === 'default' && 'border-[var(--color-border)] bg-white text-[var(--color-text)]',
            className
        )}
        {...props}
    />
));
Toast.displayName = ToastPrimitive.Root.displayName;

const ToastTitle = React.forwardRef(({ className, ...props }, ref) => (
    <ToastPrimitive.Title ref={ref} className={cn('text-sm font-semibold', className)} {...props} />
));
ToastTitle.displayName = ToastPrimitive.Title.displayName;

const ToastClose = React.forwardRef(({ className, ...props }, ref) => (
    <ToastPrimitive.Close ref={ref} className={cn('ml-auto opacity-70 hover:opacity-100', className)} toast-close="" {...props}>
        <X className="h-4 w-4" />
    </ToastPrimitive.Close>
));
ToastClose.displayName = ToastPrimitive.Close.displayName;

export { ToastProvider, ToastViewport, Toast, ToastTitle, ToastClose };
