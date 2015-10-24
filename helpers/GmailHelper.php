<?php

namespace machour\yii2\google\gmail\helpers;

use Google_Service_Gmail_Message;
use Google_Service_Gmail_Thread;

/**
 * A helper utility to deal with Google_Service_Gmail types
 *
 * Class GmailHelper
 * @package app\helpers
 */
class GmailHelper
{

    /**
     * Gets the thread subject, i.e the first message subject
     *
     * @param Google_Service_Gmail_Thread $thread
     * @return null|string
     */
    public static function getThreadSubject($thread)
    {
        return self::getMessageSubject($thread->getMessages()[0]);
    }

    /**
     * Gets the last participant from a thread
     *
     * @param Google_Service_Gmail_Thread $thread
     * @return null|string
     */
    public static function getThreadLastParticipant($thread)
    {
        return self::getMessageHeader(self::getThreadLastMessage($thread), 'From');
    }

    /**
     * Gets the last message in the given thread
     *
     * @param Google_Service_Gmail_Thread $thread
     * @return Google_Service_Gmail_Message The last message
     */
    public static function getThreadLastMessage($thread)
    {
        $messages = $thread->getMessages();
        return $messages[count($messages) - 1];
    }

    /**
     * Gets the message subject
     *
     * @param Google_Service_Gmail_Message $message
     * @return null|string
     */
    public static function getMessageSubject($message)
    {
        return self::getMessageHeader($message, 'Subject');
    }

    /**
     * Gets a named header from a message
     *
     * @param Google_Service_Gmail_Message $message
     * @param string $name The header name
     * @return null|string The header value or null if it was not found
     */
    public static function getMessageHeader($message, $name)
    {
        foreach ($message->getPayload()->getHeaders() as $header) {
            if ($header->name == $name) {
                return $header->value;
            }
        }
        return null;
    }

    /**
     * Gets the number of messages in the given thread
     *
     * @param Google_Service_Gmail_Thread $thread
     * @return int The messages count
     */
    public static function getThreadMessagesCount($thread)
    {
        return count($thread->getMessages());
    }

    /**
     * Gets the messages body (html & text)
     *
     * @param Google_Service_Gmail_Message $message
     * @return Array An array with 'html' and 'text' keys
     */
    public static function getMessageBody($message)
    {
        $ret = ['html' => '', 'text' => ''];

        $parts = $message->getPayload()->getParts();

        if (!empty($parts)) {
            foreach ($parts as $part) {
                switch ($part->mimeType) {
                    case 'text/html':
                        $ret['html'] = self::decodeData($part['body']->data);
                        break;
                    case 'plain/text':
                        $ret['text'] = self::decodeData($part['body']->data);
                        break;
                    default:
                        var_dump($part);
                        continue;
                }
            }
        } else {
            $part = $message->getPayload();
            switch ($part->mimeType) {
                case 'text/html':
                    $ret['html'] = self::decodeData($part['body']->data);
                    break;
                case 'plain/text':
                    $ret['text'] = self::decodeData($part['body']->data);
                    break;
                default:
                    continue;
            }
        }
        return $ret;
    }

    /**
     * Decodes a body part data
     *
     * @param string $data Body data
     * @return string The decoded string
     */
    private static function decodeData($data)
    {
        return base64_decode(strtr($data,'-_', '+/'));
    }
}