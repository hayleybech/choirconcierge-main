import {useAudioPlayer} from "react-use-audio-player";
import {PlayerContext} from "../../contexts/player-context";
import Button from "../inputs/Button";
import React, {useState} from "react";
import Icon from "../Icon";
import DeleteDialog from "../DeleteDialog";

const SongAttachmentList = ({ attachment_categories, song, currentPdf, setCurrentPdf }) => {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
    const [deletingAttachmentId, setDeletingAttachmentId] = useState(0);
    const { load } = useAudioPlayer();

    function play(attachment, player) {
        load({
            src: attachment.download_url,
            autoplay: true,
        });
        player.play(attachment);
    }

    function isCurrentPdf(attachment) {
        return currentPdf && attachment.id === currentPdf.id;
    }

        return (
        <>
            <PlayerContext.Consumer>
                {player => (
                    <nav className="h-full overflow-y-auto" aria-label="Directory">
                        {Object.keys(attachment_categories).map((category_title) => (
                            <div key={category_title} className="relative border-b border-gray-200">
                                <div className="z-10 sticky top-0 border-b border-gray-200 bg-gray-50 px-6 py-1 text-sm font-medium text-gray-500">
                                    <h3>{category_title}</h3>
                                </div>
                                <ul role="list" className="relative z-0 divide-y divide-gray-200">
                                    {Object.values(attachment_categories[category_title]).map(attachment => (
                                        <li key={attachment.id} className="bg-white">
                                            <div className="px-6 py-5 flex items-center space-x-3 hover:bg-gray-50">
                                                <div className="flex-shrink-0">
                                                    {isPlayable(attachment) && (
                                                        <Button variant="clear" onClick={() => play(attachment, player)} size="sm" disabled={isCurrentTrack(attachment, player)}>
                                                            <Icon icon={isCurrentTrack(attachment, player) ? 'waveform' : 'play'} />
                                                        </Button>
                                                    )}
                                                    {isPdf(attachment) && (
                                                        <Button variant="clear" onClick={() => setCurrentPdf(attachment)} size="sm" disabled={isCurrentPdf(attachment)}>
                                                            <Icon icon={isCurrentPdf(attachment) ? 'book-open' : 'book'} />
                                                        </Button>
                                                    )}
                                                </div>
                                                <div className="flex-1 min-w-0">
                                                    <p className="text-sm font-medium text-gray-900 break-all">{attachment.title !== '' ? attachment.title : attachment.filepath}</p>
                                                </div>
                                                { song.can['update_song'] &&
                                                    <Button
                                                        variant="clear"
                                                        className="text-red-500"
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
                )}
            </PlayerContext.Consumer>
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

function isPlayable(attachment) {
    return [
        'Learning Tracks',
        'Full Mix (Demo)'
    ].includes(attachment.category.title);
}

function isCurrentTrack(attachment, player) {
    return player.src === attachment.download_url;
}

function isPdf(attachment) {
    return ['Sheet Music'].includes(attachment.category.title);
}
