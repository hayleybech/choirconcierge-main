import React, {useEffect, useState} from 'react';
import {Link, useForm, usePage} from "@inertiajs/react";
import VoicePartTag from "../../components/VoicePartTag";
import SingerCategoryTag from "../../components/SingerCategoryTag";
import Table, {TableCell} from "../../components/Table";
import Icon from "../../components/Icon";
import SingerStatus from "../../SingerStatus";
import FeeStatus from "../../components/FeeStatus";
import Dialog from "../../components/Dialog";
import Button from "../../components/inputs/Button";
import {DateTime} from "luxon";
import Form from "../../components/Form";
import Label from "../../components/inputs/Label";
import DayInput from "../../components/inputs/Day";
import Error from "../../components/inputs/Error";
import Help from "../../components/inputs/Help";
import ButtonGroup from "../../components/inputs/ButtonGroup";
import DateTag from "../../components/DateTag";
import collect from "collect.js";
import TableHeadingSort from "../../components/TableHeadingSort";
import useRoute from "../../hooks/useRoute";

const SingerTableDesktop = ({ singers, sortFilterForm }) => {
    const [feeDialogIsOpen, setFeeDialogIsOpen] = useState(false);
    const [renewingSinger, setRenewingSinger] = useState(singers[0] ?? null);

    const { can, tenant } = usePage().props;
    const { route } = useRoute();

    const headings = collect({
            name: <TableHeadingSort form={sortFilterForm} sort="full-name">Name</TableHeadingSort>,
            part: tenant.ensembles.length > 1 ? 'Ensembles' : 'Voice Part',
            status: <TableHeadingSort form={sortFilterForm} sort="status-title">Status</TableHeadingSort>,
            email: 'Email',
            fees: <TableHeadingSort form={sortFilterForm} sort="paid_until">Fees</TableHeadingSort>,
        })
        .filter((item, key) => key !== 'fees' || can['manage_finances']);

    return (
        <>
            <Table
                headings={headings}
                body={singers.map((singer) => (
                    <tr key={singer.id}>
                        <TableCell>
                            <div className="flex items-center">
                                <div className="shrink-0 h-10 w-10">
                                    <img className="h-10 w-10 rounded-md" src={singer.user.avatar_url} alt={singer.user.name} />
                                </div>
                                <div className="ml-4">
                                    <Link href={route('singers.show', {singer: singer.id})} className="text-sm font-medium text-purple-800">{singer.user.name}</Link>
                                    <div>
                                        <Icon icon="phone" mr className="text-gray-400" />
                                        {singer.user.phone ? <a href={`tel:${singer.user.phone}`} target="_blank">{singer.user.phone}</a> : 'No phone'}
                                    </div>
                                </div>
                            </div>
                        </TableCell>
                        <TableCell>
                          <ul>
                          {singer.enrolments.map((enrolment) => (
                            <li key={enrolment.id} className="flex gap-2 items-center mb-2">
                              {tenant.ensembles.length > 1 && (
                                <div className="lg:w-36 xl:w-48 overflow-hidden text-ellipsis">
                                  <strong>{enrolment.ensemble.name}</strong>
                                </div>
                              )}
                              {enrolment.voice_part && <VoicePartTag title={enrolment.voice_part.title} colour={enrolment.voice_part.colour} />}
                            </li>
                          ))}
                          </ul>
                        </TableCell>
                        <TableCell>
                            <SingerCategoryTag status={new SingerStatus(singer.category.slug)} withLabel />
                        </TableCell>
                        <TableCell>
                            <Icon icon="envelope" mr className="text-gray-400" />
                            <a href={`mailto:${singer.user.email}`} target="_blank">{singer.user.email}</a>
                        </TableCell>
                        {can['manage_finances'] && (
                        <TableCell>
                            <FeeStatus status={singer.fee_status} />

                            {singer.paid_until && (
                            <div className="text-sm text-gray-500 italic mb-2">
                                <DateTag date={singer.paid_until} />
                            </div>
                            )}

                            <Button variant="secondary" size="xs" onClick={() => { setRenewingSinger(singer); setFeeDialogIsOpen(true); }}>
                                Renew Fees
                            </Button>
                        </TableCell>
                        )}
                    </tr>
                ))}
            />
            <RenewFeesDialog isOpen={feeDialogIsOpen} setIsOpen={setFeeDialogIsOpen} singer={renewingSinger} />
        </>
    );
}

export default SingerTableDesktop;

const RenewFeesDialog = ({ isOpen, setIsOpen, singer }) => {
    const { route } = useRoute();

    const { data, setData, put, errors, transform } = useForm({
        paid_until: singer?.paid_until ? DateTime.fromISO(singer.paid_until) : null,
    });

    useEffect(() => {
        setData('paid_until', singer?.paid_until ? DateTime.fromISO(singer.paid_until) : null);
    }, [singer])

    transform((data) => ({
        ...data,
        paid_until: data.paid_until?.toISODate() ?? null,
    }));

    function submit(e) {
        e.preventDefault();
        put(route('singers.fees.update', {singer}), {
            onSuccess: () => setIsOpen(false),
        });
    }

    function renewFor(diff) {
        setData('paid_until', data.paid_until?.plus(diff) ?? DateTime.now().plus(diff));
    }

    return (
        <Dialog
            title="Mark fees as paid"
            okLabel="Save"
            onOk={submit}
            okVariant="primary"
            okMethod="delete"
            isOpen={isOpen}
            setIsOpen={setIsOpen}
        >
            <p className="mb-2">To renew a singer's fees, update their expiry date below.</p>

            <Form onSubmit={submit}>
                <div className="sm:col-span-6">
                    <Label label="Renew for" forInput="paid_until" />
                    <ButtonGroup options={[
                        { label: '1 Month', onClick: () => renewFor({ months: 1 }) },
                        { label: '1 Quarter', onClick: () => renewFor({ quarters: 1 }) },
                        { label: '6 Months', onClick: () => renewFor({ months: 6 }) },
                        { label: '1 Year', onClick: () => renewFor({ years: 1 }) },
                    ]} />

                    <hr className="mt-6 mb-4" />

                    <Label label="Membership expires" forInput="paid_until" />
                    <DayInput
                        name="paid_until"
                        hasErrors={ !! errors.paid_until }
                        value={data.paid_until?.toISODate() ?? ''}
                        updateFn={value => setData('paid_until', DateTime.fromISO(value) ?? null)}
                    />
                    <Help>Manually adjust the expiry date here.</Help>
                    {errors.paid_until && <Error>{errors.paid_until}</Error>}
                </div>
            </Form>
        </Dialog>
    )
}