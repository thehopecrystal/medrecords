import * as React from 'react';
import { cva } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const badgeVariants = cva(
    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors',
    {
        variants: {
            variant: {
                default:     'bg-[var(--color-navy-lt)] text-[var(--color-navy)]',
                navy:        'bg-[var(--color-navy)] text-white',
                success:     'bg-[var(--color-green-lt)] text-[var(--color-green)]',
                destructive: 'bg-[var(--color-red-lt)] text-[var(--color-red)]',
                warning:     'bg-[var(--color-amber-lt)] text-[var(--color-amber)]',
                secondary:   'bg-[var(--color-muted)] text-[var(--color-text-2)]',
            },
        },
        defaultVariants: { variant: 'default' },
    }
);

function Badge({ className, variant, ...props }) {
    return <div className={cn(badgeVariants({ variant }), className)} {...props} />;
}

export { Badge, badgeVariants };
