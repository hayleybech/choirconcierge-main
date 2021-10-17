import React, {useState} from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import {DateTime} from "luxon";
import Dialog from "../../components/Dialog";
import SongStatusTag from "../../components/SongStatusTag";
import PitchButton from "../../components/PitchButton";
import SongAttachmentList from "../../components/SongAttachment/SongAttachmentList";
import SongAttachmentForm from "../../components/SongAttachment/SongAttachmentForm";
import LearningSummary from "../../components/Song/LearningSummary";
import MyLearningStatus from "../../components/Song/MyLearningStatus";
import SongCategoryTag from "../../components/Song/SongCategoryTag";
import AppHead from "../../components/AppHead";

const Show = ({ song, attachment_categories, all_attachment_categories, status_count, voice_parts_count }) => {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);

    return (
        <>
            <AppHead title={`${song.title} - Songs`} />
            <PageHeader
                title={song.title}
                meta={[
                    <SongStatusTag name={song.status.title} colour={song.status.colour} withLabel />,
                    <div className="space-x-1 5">
                        {song.categories.map(category => <React.Fragment key={category.id}><SongCategoryTag category={category} /></React.Fragment>)}
                    </div>,
                    <>
                        <i className="far fa-fw fa-calendar-day mr-1.5 text-gray-400 text-md" />
                        Created {DateTime.fromJSDate(new Date(song.created_at)).toLocaleString(DateTime.DATE_MED)}
                    </>
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

            <DeleteSongDialog isOpen={deleteDialogIsOpen} setIsOpen={setDeleteDialogIsOpen} song={song} />

            <div className="bg-gray-50 flex-grow">
                <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 h-full">

                    <div className="sm:col-span-1 sm:border-r sm:border-r-gray-300 sm:order-1 flex flex-col justify-stretch">
                        <SongAttachmentList attachment_categories={attachment_categories} song={song} />
                        { song.can['update_song'] && <SongAttachmentForm categories={all_attachment_categories} song={song} />}
                    </div>

                    <div className="sm:col-span-2 xl:col-span-2 sm:order-3 xl:order-2">
                        {/* PDF Viewer goes here */}
                    </div>

                    <div className="sm:col-span-1 sm:order-2 xl:order-3 sm:border-l sm:border-l-gray-300 sm:divide-y sm:divide-y-gray-300">

                        <MyLearningStatus song={song} />

                        { song.can['update_song'] && <LearningSummary status_count={status_count} voice_parts_count={voice_parts_count} song={song} />}

                    </div>

                </div>
            </div>
        </>
    );
}

Show.layout = page => <Layout children={page} />

export default Show;

const DeleteSongDialog = ({ isOpen, setIsOpen, song }) => (
    <Dialog
        title="Delete Song"
        okLabel="Delete"
        okUrl={route('songs.destroy', song)}
        okVariant="danger-solid"
        okMethod="delete"
        isOpen={isOpen}
        setIsOpen={setIsOpen}
    >
        <p>
            Are you sure you want to delete this song?
            All of its attachments will be permanently removed from our servers forever.
            This action cannot be undone.
        </p>
    </Dialog>
);