import React, {useState} from 'react'
import Layout from "../../Layouts/Layout";
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
import MediaQuery from "react-responsive";

const Show = ({ song, attachment_categories, all_attachment_categories, status_count, voice_parts_count }) => {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
    const [showFullscreenPdf, setShowFullscreenPdf] = useState(false);

    return (
        <>
            <AppHead title={`${song.title} - Songs`} />

            {showFullscreenPdf ? (
                <Pdf
                    filename={attachment_categories['Sheet Music'][0].download_url}
                    isFullscreen={showFullscreenPdf}
                    openFullscreen={() => setShowFullscreenPdf(true)}
                    closeFullscreen={() => setShowFullscreenPdf(false)}
                />
            ) : <>
                <PageHeader
                    title={song.title}
                    meta={[
                        <SongStatusTag name={song.status.title} colour={song.status.colour} withLabel />,
                        <div className="space-x-1 5">
                            {song.categories.map(category => <React.Fragment key={category.id}><SongCategoryTag category={category} /></React.Fragment>)}
                        </div>,
                        <DateTag date={song.created_at} label="Created" />,
                    ]}
                    breadcrumbs={[
                        { name: 'Dashboard', url: route('dash')},
                        { name: 'Songs', url: route('songs.index')},
                        { name: song.title, url: route('songs.show', song) },
                    ]}
                    actions={[
                        <PitchButton note={song.pitch.split('/')[0]} />,
                        { label: 'Edit', icon: 'edit', url: route('songs.edit', song), can: 'update_song' },
                        { label: 'Delete', icon: 'trash', onClick: () => setDeleteDialogIsOpen(true), variant: 'danger-outline', can: 'delete_song' },
                    ].filter(action => action.can ? song.can[action.can] : true)}
                />

                <DeleteDialog title="Delete Song" url={route('songs.destroy', song)} isOpen={deleteDialogIsOpen} setIsOpen={setDeleteDialogIsOpen}>
                    Are you sure you want to delete this song?
                    All of its attachments will be permanently removed from our servers forever.
                    This action cannot be undone.
                </DeleteDialog>

                <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 lg:overflow-y-auto">

                    <div className="sm:col-span-1 sm:border-r sm:border-r-gray-300 sm:order-1 flex flex-col justify-stretch">
                        <SongAttachmentList attachment_categories={attachment_categories} song={song} showPdf={() => setShowFullscreenPdf(true)} />
                        { song.can['update_song'] && <SongAttachmentForm categories={all_attachment_categories} song={song} />}
                    </div>

                    <MediaQuery minWidth={1024}>
                        {! showFullscreenPdf && (
                        <div className="hidden md:block sm:col-span-2 xl:col-span-2 sm:order-3 xl:order-2 overflow-hidden">
                            <Pdf
                                filename={attachment_categories['Sheet Music'][0].download_url}
                                isFullscreen={showFullscreenPdf}
                                openFullscreen={() => setShowFullscreenPdf(true)}
                                closeFullscreen={() => setShowFullscreenPdf(false)}
                            />
                        </div>
                        )}
                    </MediaQuery>

                    <div className="sm:col-span-1 sm:order-2 xl:order-3 sm:border-l sm:border-l-gray-300 sm:divide-y sm:divide-y-gray-300">

                        <MyLearningStatus song={song} />

                        { song.can['update_song'] && <LearningSummary status_count={status_count} voice_parts_count={voice_parts_count} song={song} />}

                    </div>

                </div>
            </>}
        </>
    );
}

Show.layout = page => <Layout children={page} />

export default Show;