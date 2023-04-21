import {useAudioPlayer} from "react-use-audio-player";
import Button from "../inputs/Button";
import React, {useState} from "react";
import Icon from "../Icon";
import DeleteDialog from "../DeleteDialog";
import AttachmentType from "../../AttachmentType";
import useRoute from "../../hooks/useRoute";
import {useForm} from "@inertiajs/inertia-react";
import Dialog from "../Dialog";
import Form from "../Form";
import Label from "../inputs/Label";
import Error from "../inputs/Error";
import classNames from "../../classNames";

const SongAttachmentList = ({ attachmentTypes, song, currentPdf, setCurrentPdf, player }) => {
    const { route } = useRoute();

    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
    const [deletingAttachmentId, setDeletingAttachmentId] = useState(0);
    const [attachmentToRename, setAttachmentToRename] = useState(null);
    const { load } = useAudioPlayer();

    const play = (attachment) => {
        load({
            src: attachment.download_url,
            autoplay: true,
        });
        player.play(attachment);
    };

    const openAttachment = (attachment) => {
        if(isAudio(attachment)) {
            play(attachment);
        }

        if(isPdf(attachment)) {
            setCurrentPdf(attachment);
        }
    }

    const isAudio = (attachment) => AttachmentType.get(attachment.type).isAudio;
    const isCurrentTrack = (attachment) => player.src === attachment.download_url;

    const isVideo = (attachment) => AttachmentType.get(attachment.type).isVideo;

    const isPdf = (attachment) => AttachmentType.get(attachment.type).isPdf;
    const isCurrentPdf = (attachment) => currentPdf && attachment.id === currentPdf.id;

    return (
        <>
            <nav className="h-full overflow-y-auto" aria-label="Directory">
                {Object.keys(attachmentTypes).map((typeSlug) => (
                    <div key={typeSlug} className="relative border-b border-gray-200">
                        <div className="z-10 sticky top-0 border-b border-gray-200 bg-gray-50 px-6 py-1 text-sm font-medium text-gray-500">
                            <h3>{AttachmentType.get(typeSlug).title}</h3>
                        </div>
                        <ul role="list" className="relative z-0 divide-y divide-gray-200">
                            {Object.values(attachmentTypes[typeSlug]).map(attachment => (
                                <li key={attachment.id} className="bg-white hover:bg-purple-100">
                                    <div className="flex items-center space-x-1 pr-6">
                                        <a href={isVideo(attachment) ? attachment.filepath : '#'} target={isVideo(attachment) ? '_blank' : '_self'} onClick={() => openAttachment(attachment)} className="flex-1 min-w-0 flex py-5 px-6 gap-x-3 items-center group">
                                            <div className="shrink-0">
                                                {isAudio(attachment) && (
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
                                                variant="clear"
                                                size="sm"
                                                onClick={() => setAttachmentToRename(attachment)}
                                            >
                                                <Icon icon="edit" />
                                            </Button>
                                        }
                                        { song.can['update_song'] &&
                                            <Button
                                                variant="danger-clear"
                                                size="sm"
                                                onClick={() => {setDeletingAttachmentId(attachment.id);
                                                setDeleteDialogIsOpen(true)}}
                                            >
                                                <Icon icon="trash" />
                                            </Button>
                                        }
                                        {isVideo(attachment) ? (
                                            <Button href={attachment.filepath} target="_blank" external variant="clear" size="sm" className="text-purple-500">
                                                <Icon icon="external-link" />
                                            </Button>
                                        ) : (
                                            <Button href={attachment.download_url} download={attachment.filepath} target="_blank" external variant="clear" size="sm" className="text-purple-500">
                                                <Icon icon="download" />
                                            </Button>
                                        )}
                                    </div>
                                </li>
                            ))}
                        </ul>
                    </div>
                ))}
            </nav>

            <DeleteDialog
                title="Delete Song Attachment"
                url={route('songs.attachments.destroy', {song, attachment: deletingAttachmentId})}
                isOpen={deleteDialogIsOpen}
                setIsOpen={setDeleteDialogIsOpen}
            >
                Are you sure you want to delete this attachment?
                It will be permanently removed from our servers forever.
                This action cannot be undone.
            </DeleteDialog>

            <RenameAttachmentDialog song={song} attachment={attachmentToRename} setAttachment={setAttachmentToRename} key={attachmentToRename?.id} />
        </>
    );
}

export default SongAttachmentList;

function splitFilename(filename)
{
    return [
        filename.substring(0, filename.lastIndexOf('.')) || filename,
        filename.substring(filename.lastIndexOf('.')) || '',
    ];
}

const RenameAttachmentDialog = ({ song, attachment, setAttachment }) => {
    const { route } = useRoute();

    const { data, setData, put, errors } = useForm({
        filename: attachment?.filepath || '',
    });

    const [name, extension] = splitFilename(data.filename);

    function submit(e) {
        e.preventDefault();
        put(route('songs.attachments.update', {song, attachment}), {
            onSuccess: () => setAttachment(null),
        });
    }

    return (
        <Dialog
            title="Rename file"
            okLabel="Rename"
            onOk={submit}
            okVariant="primary"
            isOpen={!!attachment}
            setIsOpen={() => setAttachment(null)}
        >
            <Form onSubmit={submit}>
                <div className="sm:col-span-6">
                    <Label label="New filename" forInput="filename" />
                    <FilenameInput name="filename" value={name} extension={extension} updateFn={value => setData('filename', (value + extension))} hasErrors={ !! errors['filename'] } />
                    {errors.filename && <Error>{errors.filename}</Error>}

                </div>
            </Form>
        </Dialog>
    )
};

const FilenameInput = ({ name, value, extension, hasErrors, updateFn, otherProps }) => (
    <div className="mt-1 flex rounded-md shadow-sm">
        <input
            type="text"
            name={name}
            id={name}
            value={value}
            onChange={e => updateFn(e.target.value)}
            className={classNames(
                'flex-1 min-w-0 block w-full px-3 py-2 sm:text-sm rounded-none rounded-l-md',
                hasErrors
                    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                    : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500'
            )}
            {...otherProps}
        />
        <span className="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
          {extension}
        </span>
    </div>
);