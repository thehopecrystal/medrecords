import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const inputStyle = { width: '100%', background: 'var(--color-card)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '9px 12px', fontSize: 13, color: 'var(--color-text)', fontFamily: 'inherit', outline: 'none', boxSizing: 'border-box' };
const textareaStyle = { ...inputStyle, resize: 'vertical', minHeight: 80 };

function Field({ label, hint, error, children, span2 }) {
    return (
        <div style={{ gridColumn: span2 ? 'span 2' : undefined }}>
            <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>{label}</label>
            {children}
            {hint && <div style={{ fontSize: 11, color: 'var(--color-text-3)', marginTop: 4 }}>{hint}</div>}
            {error && <div style={{ color: 'var(--color-red)', fontSize: 12, marginTop: 4 }}>{error}</div>}
        </div>
    );
}

export default function Form({ plan }) {
    const isEdit = !!(plan?.id);

    const { data, setData, post, put, processing, errors } = useForm({
        name:            plan?.name            ?? '',
        slug:            plan?.slug            ?? '',
        description:     plan?.description     ?? '',
        price:           plan?.price           ?? 0,
        billing_period:  plan?.billing_period  ?? 'monthly',
        badge:           plan?.badge           ?? '',
        sort_order:      plan?.sort_order      ?? 0,
        is_active:       plan?.is_active       ?? true,
        is_featured:     plan?.is_featured     ?? false,
        features_raw:    (plan?.features ?? []).join('\n'),
    });

    function submit(e) {
        e.preventDefault();
        if (isEdit) {
            put(`/admin/plans/${plan.id}`);
        } else {
            post('/admin/plans');
        }
    }

    return (
        <AppLayout title={isEdit ? 'Edit Plan' : 'Create Plan'}>
            <div style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', marginBottom: 24, gap: 12 }}>
                <div>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>
                        {isEdit ? `Edit Plan: ${plan.name}` : 'Create New Plan'}
                    </h1>
                    <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>
                        {isEdit ? 'Update the plan details below.' : 'Define a new subscription tier.'}
                    </p>
                </div>
                <Link href="/admin/plans" style={{ padding: '8px 16px', borderRadius: 8, border: '1.5px solid var(--color-border)', background: 'var(--color-card)', color: 'var(--color-text-2)', fontSize: 13, fontWeight: 600, textDecoration: 'none' }}>← Back to Plans</Link>
            </div>

            <div style={{ maxWidth: 720 }}>
                <form onSubmit={submit}>
                    {/* Plan details */}
                    <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden', marginBottom: 20 }}>
                        <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                            <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Plan Details</div>
                        </div>
                        <div style={{ padding: '20px' }}>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                                <Field label="Plan Name *" error={errors.name}>
                                    <input type="text" value={data.name} onChange={e => setData('name', e.target.value)} required placeholder="e.g. Basic" style={inputStyle} />
                                </Field>
                                <Field label="Slug" hint="Leave blank to auto-generate." error={errors.slug}>
                                    <input type="text" value={data.slug} onChange={e => setData('slug', e.target.value)} placeholder="auto-generated from name" style={inputStyle} />
                                </Field>
                                <Field label="Description" error={errors.description} span2>
                                    <textarea value={data.description} onChange={e => setData('description', e.target.value)} rows={2} placeholder="Short plan description shown to users…" style={textareaStyle} />
                                </Field>
                                <Field label="Price (₦) *" error={errors.price}>
                                    <input type="number" value={data.price} onChange={e => setData('price', e.target.value)} min={0} step="0.01" required style={inputStyle} />
                                </Field>
                                <Field label="Billing Period *" error={errors.billing_period}>
                                    <select value={data.billing_period} onChange={e => setData('billing_period', e.target.value)} required style={inputStyle}>
                                        <option value="free">Free (₦0)</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                        <option value="one-time">One-time / Contact</option>
                                    </select>
                                </Field>
                                <Field label="Badge Label" hint="Shown as a ribbon on the pricing card." error={errors.badge}>
                                    <input type="text" value={data.badge} onChange={e => setData('badge', e.target.value)} placeholder="e.g. Most Popular, Contact us" style={inputStyle} />
                                </Field>
                                <Field label="Sort Order *" hint="Lower numbers appear first." error={errors.sort_order}>
                                    <input type="number" value={data.sort_order} onChange={e => setData('sort_order', e.target.value)} min={0} required style={inputStyle} />
                                </Field>
                            </div>
                        </div>
                    </div>

                    {/* Features */}
                    <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden', marginBottom: 20 }}>
                        <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                            <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Features List</div>
                        </div>
                        <div style={{ padding: '20px' }}>
                            <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>Features (one per line)</label>
                            <textarea
                                value={data.features_raw}
                                onChange={e => setData('features_raw', e.target.value)}
                                rows={10}
                                placeholder={"Unlimited health records\nAccess code generation\n5 dependent records\n…"}
                                style={textareaStyle}
                            />
                            <div style={{ fontSize: 11, color: 'var(--color-text-3)', marginTop: 4 }}>Each line becomes one feature bullet. Empty lines are ignored.</div>
                        </div>
                    </div>

                    {/* Visibility */}
                    <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden', marginBottom: 24 }}>
                        <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                            <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Visibility</div>
                        </div>
                        <div style={{ padding: '20px', display: 'flex', gap: 24, flexWrap: 'wrap' }}>
                            <label style={{ display: 'flex', alignItems: 'center', gap: 8, cursor: 'pointer', fontSize: 13, fontWeight: 600, color: 'var(--color-text)' }}>
                                <input type="checkbox" checked={data.is_active} onChange={e => setData('is_active', e.target.checked)} style={{ width: 16, height: 16, accentColor: 'var(--color-navy)' }} />
                                Active (show on pricing page)
                            </label>
                            <label style={{ display: 'flex', alignItems: 'center', gap: 8, cursor: 'pointer', fontSize: 13, fontWeight: 600, color: 'var(--color-text)' }}>
                                <input type="checkbox" checked={data.is_featured} onChange={e => setData('is_featured', e.target.checked)} style={{ width: 16, height: 16, accentColor: 'var(--color-navy)' }} />
                                Featured (highlighted card)
                            </label>
                        </div>
                    </div>

                    <div style={{ display: 'flex', gap: 10 }}>
                        <button type="submit" disabled={processing} style={{ padding: '10px 22px', borderRadius: 9, background: processing ? '#a0aec0' : 'var(--color-navy)', color: '#fff', border: 'none', fontSize: 13, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer', fontFamily: 'inherit' }}>
                            {processing ? 'Saving…' : (isEdit ? 'Save Changes' : 'Create Plan')}
                        </button>
                        <Link href="/admin/plans" style={{ padding: '10px 22px', borderRadius: 9, border: '1.5px solid var(--color-border)', background: 'var(--color-card)', color: 'var(--color-text-2)', fontSize: 13, fontWeight: 600, textDecoration: 'none' }}>Cancel</Link>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
