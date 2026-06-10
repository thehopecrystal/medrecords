import * as React from 'react';
import { cn } from '@/lib/utils';

const Input = React.forwardRef(({ className, type, ...props }, ref) => (
    <input
        type={type}
        className={cn(
            'flex h-9 w-full rounded-[var(--radius)] border border-[var(--color-border)] bg-white px-3 py-2 text-sm text-[var(--color-text)] placeholder:text-[var(--color-text-3)] focus:outline-none focus:ring-2 focus:ring-[var(--color-navy)] focus:border-[var(--color-navy)] disabled:cursor-not-allowed disabled:opacity-50 transition-colors',
            className
        )}
        ref={ref}
        {...props}
    />
));
Input.displayName = 'Input';

export { Input };
