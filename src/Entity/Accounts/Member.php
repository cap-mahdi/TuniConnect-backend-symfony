<?php

namespace App\Entity\Accounts;

use App\Entity\Chat\Message;
use App\Entity\Chat\Room;
use App\Entity\Covoiturage\Covoiturage;
use App\Entity\Covoiturage\RequestCovoiturage;
use App\Entity\Notifications\Notification;
use App\Entity\Posts\Post;
use App\Entity\Posts\SharedPost;
use App\Repository\Accounts\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface ;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
#[ORM\Table(name: '`member`')]
class Member extends Person
{

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class)]
    private Collection $messagesSent;




    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["Member:Post" , "Member:Get" ])]
    private ?\DateTimeInterface $dateOfMembership = null;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: FriendRequest::class, orphanRemoval: true)]
    private Collection $friendRequests;

    #[ORM\ManyToMany(targetEntity: self::class)]
    private Collection $friends;

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Room::class, orphanRemoval: true)]
    private Collection $roomsCreated;

    #[ORM\ManyToMany(targetEntity: Room::class, mappedBy: 'members')]
    private Collection $roomsIn;

    #[ORM\OneToMany(mappedBy: 'sharer', targetEntity: SharedPost::class)]
    private Collection $sharedPosts;

    #[ORM\ManyToMany(targetEntity: SharedPost::class, mappedBy: 'likers')]
    private Collection $likedPosts;

    #[ORM\OneToMany(mappedBy: 'driver', targetEntity: Covoiturage::class)]
    private Collection $covoiturages;

    #[ORM\ManyToMany(targetEntity: Covoiturage::class, mappedBy: 'passengers')]
    private Collection $covoituragesTaken;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: RequestCovoiturage::class, orphanRemoval: true)]
    private Collection $requestCovoiturageSent;

    #[ORM\OneToMany(mappedBy: 'relatedTo', targetEntity: Notification::class)]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Post::class)]
    private Collection $myPosts;








    public function __construct()
    {
        $this->messagesSent = new ArrayCollection();
        $this->friendRequests = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->roomsCreated = new ArrayCollection();
        $this->roomsIn = new ArrayCollection();
        $this->sharedPosts = new ArrayCollection();
        $this->likedPosts = new ArrayCollection();
        $this->covoiturages = new ArrayCollection();
        $this->covoituragesTaken = new ArrayCollection();
        $this->requestCovoiturageSent = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->myPosts = new ArrayCollection();
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessagesSent(): Collection
    {
        return $this->messagesSent;
    }

    public function addMessagesSent(Message $messagesSent): self
    {
        if (!$this->messagesSent->contains($messagesSent)) {
            $this->messagesSent->add($messagesSent);
            $messagesSent->setSender($this);
        }

        return $this;
    }

    public function removeMessagesSent(Message $messagesSent): self
    {
        if ($this->messagesSent->removeElement($messagesSent)) {
            // set the owning side to null (unless already changed)
            if ($messagesSent->getSender() === $this) {
                $messagesSent->setSender(null);
            }
        }

        return $this;
    }



    public function getDateOfMembership(): ?\DateTimeInterface
    {
        return $this->dateOfMembership;
    }

    public function setDateOfMembership(DateTimeInterface $dateOfMembership): self
    {
        $this->dateOfMembership = $dateOfMembership;

        return $this;
    }

    /**
     * @return Collection<int, FriendRequest>
     */
    public function getFriendRequests(): Collection
    {
        return $this->friendRequests;
    }

    public function addFriendRequest(FriendRequest $friendRequest): self
    {
        if (!$this->friendRequests->contains($friendRequest)) {
            $this->friendRequests->add($friendRequest);
            $friendRequest->setSender($this);
        }

        return $this;
    }

    public function removeFriendRequest(FriendRequest $friendRequest): self
    {
        if ($this->friendRequests->removeElement($friendRequest)) {
            // set the owning side to null (unless already changed)
            if ($friendRequest->getSender() === $this) {
                $friendRequest->setSender(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(self $friend): self
    {
        if (!$this->friends->contains($friend)) {
            $this->friends->add($friend);
        }

        return $this;
    }

    public function removeFriend(self $friend): self
    {
        $this->friends->removeElement($friend);

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRoomsCreated(): Collection
    {
        return $this->roomsCreated;
    }

    public function addRoomCreated(Room $roomCreated): self
    {
        if (!$this->roomsCreated->contains($roomCreated)) {
            $this->roomsCreated->add($roomCreated);
            $roomCreated->setCreator($this);
        }

        return $this;
    }

    public function removeRoomCreated(Room $roomCreated): self
    {
        if ($this->roomsCreated->removeElement($roomCreated)) {
            // set the owning side to null (unless already changed)
            if ($roomCreated->getCreator() === $this) {
                $roomCreated->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRoomsIn(): Collection
    {
        return $this->roomsIn;
    }

    public function addRoomsIn(Room $roomsIn): self
    {
        if (!$this->roomsIn->contains($roomsIn)) {
            $this->roomsIn->add($roomsIn);
            $roomsIn->addMember($this);
        }

        return $this;
    }

    public function removeRoomsIn(Room $roomsIn): self
    {
        if ($this->roomsIn->removeElement($roomsIn)) {
            $roomsIn->removeMember($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, SharedPost>
     */
    public function getSharedPosts(): Collection
    {
        return $this->sharedPosts;
    }

    public function addSharedPost(SharedPost $sharedPost): self
    {
        if (!$this->sharedPosts->contains($sharedPost)) {
            $this->sharedPosts->add($sharedPost);
            $sharedPost->setSharer($this);
        }

        return $this;
    }

    public function removeSharedPost(SharedPost $sharedPost): self
    {
        if ($this->sharedPosts->removeElement($sharedPost)) {
            // set the owning side to null (unless already changed)
            if ($sharedPost->getSharer() === $this) {
                $sharedPost->setSharer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SharedPost>
     */
    public function getLikedPosts(): Collection
    {
        return $this->likedPosts;
    }

    public function addLikedPost(SharedPost $likedPost): self
    {
        if (!$this->likedPosts->contains($likedPost)) {
            $this->likedPosts->add($likedPost);
            $likedPost->addLiker($this);
        }

        return $this;
    }

    public function removeLikedPost(SharedPost $likedPost): self
    {
        if ($this->likedPosts->removeElement($likedPost)) {
            $likedPost->removeLiker($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Covoiturage>
     */
    public function getCovoiturages(): Collection
    {
        return $this->covoiturages;
    }

    public function addCovoiturage(Covoiturage $covoiturage): self
    {
        if (!$this->covoiturages->contains($covoiturage)) {
            $this->covoiturages->add($covoiturage);
            $covoiturage->setDriver($this);
        }

        return $this;
    }

    public function removeCovoiturage(Covoiturage $covoiturage): self
    {
        if ($this->covoiturages->removeElement($covoiturage)) {
            // set the owning side to null (unless already changed)
            if ($covoiturage->getDriver() === $this) {
                $covoiturage->setDriver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Covoiturage>
     */
    public function getCovoituragesTaken(): Collection
    {
        return $this->covoituragesTaken;
    }

    public function addCovoituragesTaken(Covoiturage $covoituragesTaken): self
    {
        if (!$this->covoituragesTaken->contains($covoituragesTaken)) {
            $this->covoituragesTaken->add($covoituragesTaken);
            $covoituragesTaken->addPassenger($this);
        }

        return $this;
    }

    public function removeCovoituragesTaken(Covoiturage $covoituragesTaken): self
    {
        if ($this->covoituragesTaken->removeElement($covoituragesTaken)) {
            $covoituragesTaken->removePassenger($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, RequestCovoiturage>
     */
    public function getRequestCovoiturageSent(): Collection
    {
        return $this->requestCovoiturageSent;
    }

    public function addRequestCovoiturageSent(RequestCovoiturage $requestCovoiturageSent): self
    {
        if (!$this->requestCovoiturageSent->contains($requestCovoiturageSent)) {
            $this->requestCovoiturageSent->add($requestCovoiturageSent);
            $requestCovoiturageSent->setSender($this);
        }

        return $this;
    }

    public function removeRequestCovoiturageSent(RequestCovoiturage $requestCovoiturageSent): self
    {
        if ($this->requestCovoiturageSent->removeElement($requestCovoiturageSent)) {
            // set the owning side to null (unless already changed)
            if ($requestCovoiturageSent->getSender() === $this) {
                $requestCovoiturageSent->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setRelatedTo($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getRelatedTo() === $this) {
                $notification->setRelatedTo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getMyPosts(): Collection
    {
        return $this->myPosts;
    }

    public function addMyPost(Post $myPost): self
    {
        if (!$this->myPosts->contains($myPost)) {
            $this->myPosts->add($myPost);
            $myPost->setOwner($this);
        }

        return $this;
    }

    public function removeMyPost(Post $myPost): self
    {
        if ($this->myPosts->removeElement($myPost)) {
            // set the owning side to null (unless already changed)
            if ($myPost->getOwner() === $this) {
                $myPost->setOwner(null);
            }
        }

        return $this;
    }



}
