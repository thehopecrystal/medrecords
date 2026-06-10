import * as React from 'react';
import { Slot } from '@radix-ui/react-slot';
import { cva } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const buttonVariants = cva(
    'inline-flex items-center justify-center gap-2 rounded-[var(--radius)] text-sm font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)] disabled:pointer-events-none disabled:opacity-50',
    {
        variants: {
            variant: {
                primary:    'bg-[var(--color-navy)] text-white hover:bg-[var(--color-navy-md)] shadow-sm',
                secondary:  'bg-white text-[var(--color-text)] border border-[var(--color-border)] hover:bg-[var(--color-navy-lt)] hover:border-[var(--color-navy)]',
                ghost:      'text-[var(--color-text-2)] hover:bg-[var(--color-navy-lt)] hover:text-[var(--color-navy)]',
                destructive:'bg-[var(--color-red)] text-white hover:opacity-90',
                success:    'bg-[var(--color-green)] text-white hover:opacity-90',
                outline:    'border border-[var(--color-border)] bg-white hover:bg-[var(--color-navy-lt)]',
                link:       'text-[var(--color-navy)] underline-offset-4 hover:underline p-0 h-auto',
            },
            size: {
                default: 'h-9 px-4 py-2',
                sm:      'h-7 px-3 text-xs',
                lg:      'h-11 px-6 text-base',
                icon:    'h-9 w-9',
            },
        },
        defaultVariants: {
            variant: 'primary',
            size: 'default',
        },
    }
);

const Button = React.forwardRef(({ className, variant, size, asChild = false, ...props }, ref) => {
    const Comp = asChild ? Slot : 'button';
    return (
        <Comp
            className={cn(buttonVariants({ variant, size, className }))}
            ref={ref}
            {...props}
        />
    );
});
Button.displayName = 'Button';

export { Button, buttonVariants };
