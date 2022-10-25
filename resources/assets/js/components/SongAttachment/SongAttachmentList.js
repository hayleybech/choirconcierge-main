import {useAudioPlayer} from "react-use-audio-player";
import Button from "../inputs/Button";
import React, {useState} from "react";
import Icon from "../Icon";
import DeleteDialog from "../DeleteDialog";

const SongAttachmentList = ({ attachment_categories, song, currentPdf, setCurrentPdf, player }) => {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
    const [deletingAttachmentId, setDeletingAttachmentId] = useState(0);
    const { load } = useAudioPlayer();

    const play = (attachment) => {
        load({
            src: attachment.download_url,
            autoplay: true,
        });
        player.play(attachment);
    };

    const openAttachment = (attachment) => {
        if(isPlayable(attachment)) {
            play(attachment);
        }

        if(isPdf(attachment)) {
            setCurrentPdf(attachment);
        }
    }

    const isPlayable = (attachment) => ['Learning Tracks', 'Full Mix (Demo)'].includes(attachment.category.title);
    const isCurrentTrack = (attachment) => player.src === attachment.download_url;

    const isPdf = (attachment) => ['Sheet Music'].includes(attachment.category.title);
    const isCurrentPdf = (attachment) => currentPdf && attachment.id === currentPdf.id;

    return (
        <>
            <nav className="h-full overflow-y-auto" aria-label="Directory">
                {Object.keys(attachment_categories).map((category_title) => (
                    <div key={category_title} className="relative border-b border-gray-200">
                        <div className="z-10 sticky top-0 border-b border-gray-200 bg-gray-50 px-6 py-1 text-sm font-medium text-gray-500">
                            <h3>{category_title}</h3>
                        </div>
                        <ul role="list" className="relative z-0 divide-y divide-gray-200">
                            {Object.values(attachment_categories[category_title]).map(attachment => (
                                <li key={attachment.id} className="bg-white hover:bg-purple-100">
                                    <div className="flex items-center space-x-3 pr-6">
                                        <a href="#" onClick={() => openAttachment(attachment)} className="flex-1 min-w-0 flex py-5 px-6 gap-x-3 items-center group">
                                            <div className="shrink-0">
                                                {isPlayable(attachment) && (
                                                    <Icon icon={isCurrentTrack(attachment) ? 'waveform' : 'play'} className="text-sm text-gray-700 group-hover:text-purple-600" />
                                                )}
                                                {isPdf(attachment) && (
                                                    <Icon icon={isCurrentPdf(attachment) ? 'book-open' : 'book'} className="text-sm text-gray-700 group-hover:text-purple-600" />
                                                )}
                                            </div>
                                            <div>
                                                <p className="text-sm font-medium text-gray-900 group-hover:text-purple-600 break-all">{attachment.title !== '' ? attachment.title : attachment.filepath}</p>
                                            </div>
                                        </a>
                                        { song.can['update_song'] &&
                                            <Button
                                                variant="danger-clear"
                                                onClick={() => {setDeletingAttachmentId(attachment.id);
                                                setDeleteDialogIsOpen(true)}}
                                            >
                                                <Icon icon="trash" />
                                            </Button>
                                        }
                                        <>
                                            <a href={attachment.download_url} download={attachment.filepath} className="text-purple-500">
                                                <Icon icon="download" />
                                            </a>
                                        </>
                                    </div>
                                </li>
                            ))}
                        </ul>
                    </div>
                ))}
            </nav>

            <DeleteDialog
                title="Delete Song Attachment"
                url={route('songs.attachments.destroy', [song, deletingAttachmentId])}
                isOpen={deleteDialogIsOpen}
                setIsOpen={setDeleteDialogIsOpen}
            >
                Are you sure you want to delete this attachment?
                It will be permanently removed from our servers forever.
                This action cannot be undone.
            </DeleteDialog>
        </>
    );
}

export default SongAttachmentList;
