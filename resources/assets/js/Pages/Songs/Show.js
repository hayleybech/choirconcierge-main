import React, {useContext, useEffect, useState} from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import SongStatusTag from "../../components/SongStatusTag";
import PitchButton from "../../components/PitchButton";
import SongAttachmentList from "../../components/SongAttachment/SongAttachmentList";
import SongAttachmentForm from "../../components/SongAttachment/SongAttachmentForm";
import LearningSummary from "../../components/Song/LearningSummary";
import MyLearningStatus from "../../components/Song/MyLearningStatus";
import SongCategoryTag from "../../components/Song/SongCategoryTag";
import AppHead from "../../components/AppHead";
import DateTag from "../../components/DateTag";
import DeleteDialog from "../../components/DeleteDialog";
import Pdf from "../../components/Song/Pdf";
import {useMediaQuery} from "react-responsive";
import {PlayerContext} from "../../contexts/player-context";
import SongStatus from "../../SongStatus";
import Icon from "../../components/Icon";
import Prose from "../../components/Prose";
import ButtonLink from "../../components/inputs/ButtonLink";
import CollapsePanel from "../../components/CollapsePanel";
import CollapseGroup from "../../components/CollapseGroup";
import EmptyState from "../../components/EmptyState";
import {Synth} from "tone";
import useRoute from "../../hooks/useRoute";

const Show = ({ song, attachment_types, status_count, voice_parts_count }) => {
    const { route } = useRoute();

    const [synth] = useState(() => new Synth().toDestination());
    const player = useContext(PlayerContext);

    const isMobile = useMediaQuery({ query: '(max-width: 1023px)' });
    const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });

    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
    const [currentPdf, setCurrentPdf] = useState(() => {
        if(isMobile) {
            return null;
        }
        if(! attachment_types['sheet-music']) {
            return null;
        }
         return attachment_types['sheet-music'][0];
    });

    const showPdf = (attachment) => {
        setCurrentPdf(attachment);

        if(isMobile) {
            player.setShowFullscreen(true);
        }
    };

    const openFullscreen = () => player.setShowFullscreen(true);
    const closeFullscreen = () => player.setShowFullscreen(false);
    const closeFullscreenMobile = () => {
        player.setShowFullscreen(false);

        if(isMobile) {
            setCurrentPdf(null);
        }
    };

    useEffect(() => {
        return () => {
            closeFullscreenMobile();
        }
    }, []);

    return (
        <>
            <AppHead title={`${song.title} - Songs`} />

            {player.showFullscreen ? (
                <Pdf
                    filename={currentPdf?.download_url}
                    isFullscreen={player.showFullscreen}
                    openFullscreen={openFullscreen}
                    closeFullscreen={closeFullscreenMobile}
                    synth={synth}
                    pitch={song.pitch.split('/')[0]}
                />
            ) : <>
                <PageHeader
                    title={song.title}
                    meta={[
                        <SongStatusTag status={new SongStatus(song.status.slug)} withLabel />,
                        <div className="space-x-1 5">
                            {song.categories.map(category => <React.Fragment key={category.id}><SongCategoryTag category={category} /></React.Fragment>)}
                        </div>,
                        <DateTag date={song.created_at} label="Created" />,
                        !!song.show_for_prospects && (
                            <div>
                                <Icon icon="microphone-stand" mr className="text-sm text-emerald-500" />
                                <span className="text-sm font-medium text-gray-500 truncate">Audition Song</span>
                            </div>
                        ),
                    ]}
                    breadcrumbs={[
                        { name: 'Dashboard', url: route('dash')},
                        { name: 'Songs', url: route('songs.index')},
                        { name: song.title, url: route('songs.show', {song}) },
                    ]}
                    actions={[
                        <PitchButton synth={synth} note={song.pitch.split('/')[0]} />,
                        { label: 'Edit', icon: 'edit', url: route('songs.edit', {song}), can: 'update_song' },
                        { label: 'Delete', icon: 'trash', onClick: () => setDeleteDialogIsOpen(true), variant: 'danger-outline', can: 'delete_song' },
                    ].filter(action => action.can ? song.can[action.can] : true)}
                />

                <DeleteDialog
                    title="Delete Song"
                    url={route('songs.destroy', {song})}
                    isOpen={deleteDialogIsOpen}
                    setIsOpen={setDeleteDialogIsOpen}
                >
                    Are you sure you want to delete this song?
                    All of its attachments will be permanently removed from our servers forever.
                    This action cannot be undone.
                </DeleteDialog>

                <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 lg:overflow-y-auto divide-y divide-gray-300 sm:divide-y-0 sm:divide-x">

                    <div className="sm:col-span-1 sm:border-r sm:border-r-gray-300 sm:order-1 flex flex-col justify-stretch">
                        {song.attachments.length > 0
                            ? (
                                <SongAttachmentList
                                    attachmentTypes={attachment_types}
                                    song={song}
                                    currentPdf={currentPdf}
                                    setCurrentPdf={showPdf}
                                    player={player}
                                />
                            ) : (
                                <EmptyState
                                    title="No attachments"
                                    description={<>
                                        Looks like there are no attachments for this song yet. <br />
                                        This is the perfect place to store sheet music, learning tracks and more!<br/>
                                    </>}
                                    actionDescription={song.can['update_song'] ? "To get started, use the form below." : null}
                                    icon="file-music"
                                />
                            )
                        }
                        { song.can['update_song'] && <SongAttachmentForm song={song} />}
                    </div>

                    {isDesktop && ! player.showFullscreen && (
                        <div className="hidden md:block sm:col-span-2 xl:col-span-2 sm:order-3 xl:order-2 overflow-hidden">
                            {!!currentPdf
                                ? (
                                <Pdf
                                    filename={currentPdf?.download_url}
                                    isFullscreen={player.showFullscreen}
                                    openFullscreen={openFullscreen}
                                    closeFullscreen={closeFullscreen}
                                    synth={synth}
                                    pitch={song.pitch.split('/')[0]}
                                />
                                ) : (
                                    <EmptyState
                                        title="No sheet music"
                                        description={<>
                                            This prime location is reserved for displaying your sheet music. <br />
                                            It looks like you don't have any yet for this song.
                                        </>}
                                        actionDescription={song.can['update_song'] ? 'To add some, use the "Add Attachment" form on the left.' : null}
                                        icon="file-pdf"
                                    />
                                )
                            }
                        </div>

                    )}

                    <div className="sm:col-span-1 sm:order-2 xl:order-3">
                        <CollapseGroup items={[
                            { title: 'Song Description', show: true, defaultOpen: song.description?.length > 0, content: <SongDescription description={song.description} /> },
                            { title: 'My Learning Status', show: true, content: <MyLearningStatus song={song} /> },
                            {
                                title: 'Learning Summary',
                                show: song.can['update_song'],
                                action: <EditLearningSummaryButton song={song} />,
                                content: <LearningSummary status_count={status_count} voice_parts_count={voice_parts_count} song={song} />,
                            },
                        ]} />
                    </div>

                </div>
            </>}
        </>
    );
}

Show.layout = page => <TenantLayout children={page} />

export default Show;

const SongDescription = ({ description }) => <CollapsePanel><Prose content={description ?? 'No description'} className="mb-8" /></CollapsePanel>;

const EditLearningSummaryButton = ({ song }) => {
    const { route } = useRoute();

    return (
        <ButtonLink variant="primary" size="sm" href={route('songs.singers.index', {song})}>
            <Icon icon="edit" />
            Edit
        </ButtonLink>
    );
}