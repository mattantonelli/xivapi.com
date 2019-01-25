<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="users",
 *     indexes={
 *          @ORM\Index(name="added", columns={"added"}),
 *          @ORM\Index(name="is_new", columns={"is_new"}),
 *          @ORM\Index(name="is_banned", columns={"is_banned"}),
 *          @ORM\Index(name="is_locked", columns={"is_locked"}),
 *
 *          @ORM\Index(name="sso", columns={"sso"}),
 *          @ORM\Index(name="ssoId", columns={"ssoId"}),
 *          @ORM\Index(name="session", columns={"session"}),
 *          @ORM\Index(name="username", columns={"username"}),
 *          @ORM\Index(name="email", columns={"email"}),
 *          @ORM\Index(name="appsMax", columns={"appsMax"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User extends UserCommon
{
    /**
     * The name of the SSO provider
     * @var string
     * @ORM\Column(type="string", length=32)
     */
    private $sso;
    /**
     * @var string
     * @ORM\Column(type="string", length=128, unique=true)
     */
    private $ssoId;
    /**
     * @var string
     * A random hash saved to cookie to retrieve the token
     * @ORM\Column(type="string", length=128, unique=true)
     */
    private $session;
    /**
     * @var string
     * The token provided by the SSO provider
     * @ORM\Column(type="text", length=512, nullable=true)
     */
    private $token;
    /**
     * @var string
     * Username provided by the SSO provider (updates on token refresh)
     * @ORM\Column(type="string", length=64)
     */
    private $username;
    /**
     * @var string
     * Email provided by the SSO token, this is considered "unique", if someone changes their
     * email then this would in-affect create a new account.
     * @ORM\Column(type="string", length=128)
     */
    private $email;
    /**
     * Either provided by SSO provider or default
     *
     *  DISCORD: https://cdn.discordapp.com/avatars/<USER ID>/<AVATAR ID>.png?size=256
     *
     * @var string
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $avatar;
    /**
     * @ORM\OneToMany(targetEntity="App", mappedBy="user")
     */
    private $apps;
    /**
     * @var int
     * @ORM\Column(type="integer", length=16)
     */
    private $appsMax = 1;

    public function __construct()
    {
        parent::__construct();
        $this->session = Uuid::uuid4()->toString() . Uuid::uuid4()->toString() . Uuid::uuid4()->toString();
        $this->apps = new ArrayCollection();
    }

    // -------------------------------------------------------

    /**
     * Check ban status (will redirect if they're ban)
     */
    public function checkBannedStatusAndRedirectUserToDiscord()
    {
        if ($this->isBanned()) {
            header("Location: https://discord.gg/MFFVHWC");
            die();
        }
    }

    // -------------------------------------------------------

    public function getSso(): string
    {
        return $this->sso;
    }

    public function setSso(string $sso)
    {
        $this->sso = $sso;

        return $this;
    }

    public function getSsoId(): string
    {
        return $this->ssoId;
    }

    public function setSsoId(string $ssoId)
    {
        $this->ssoId = $ssoId;

        return $this;
    }

    public function getSession(): string
    {
        return $this->session;
    }

    public function setSession(string $session)
    {
        $this->session = $session;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token)
    {
        $this->token = $token;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    public function getAvatar(): string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getApps()
    {
        return $this->apps;
    }

    public function setApps($apps)
    {
        $this->apps = $apps;

        return $this;
    }

    public function getAppsMax(): int
    {
        return $this->appsMax;
    }

    public function setAppsMax(int $appsMax)
    {
        $this->appsMax = $appsMax;

        return $this;
    }
}
