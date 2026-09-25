# 5. Facilities, wings and cages

[← Inviting users](04-users-and-invitations.md) · [Documentation index](README.md) · [Next: The dashboard →](06-dashboard.md)

> **Who:** Manager and Staff. Viewers can open these pages but cannot change anything.

Before you register animals, describe where they will live. A shelter's space is organised in three levels:

```
Facility   (a physical site with an address, e.g. "Main Building")
 └── Wing  (an area inside it, e.g. "Dog Wing A", "Cattery")
      └── Cage  (where the animals are placed, with a code and a capacity)
```

The **total capacity of all cages** is the number of animals the shelter can house. It feeds the *Available Capacity* counter on the dashboard. Foster family wings are the exception: they are not shelter space (see [5.7](#57-foster-families)).

## 5.1 The Facilities page

Open **Facilities** in the sidebar. Each facility is a large card holding its wings, and each wing lists its cages.

![Facilities, wings and cages](screenshots/31-facilities.png)

The badges show how full each level is, for example **18 of 38 free**. Each cage's badge is coloured by occupancy:

| Colour | Meaning |
|--------|---------|
| 🟢 Green | Plenty of room |
| 🟡 Yellow | More than 80% full |
| 🔴 Red | Full: no free places |

Each cage row also shows the **species** it is meant for (*Dog*, *Cat*), and three icons:

- 👁 **eye** — see the animals in the cage;
- ✏️ **pencil** — edit the cage;
- 🗑 **bin** — delete the cage.

## 5.2 Step 1 — Create a facility

1. Click **Create New** (top right).
2. Fill in the **Name** (e.g. *Main Building*), and optionally the **Address**, **Postal Code**, **City** and **Operational Notes**.
3. Click **Create New**.

![Create a facility](screenshots/31a-facility-create.png)

## 5.3 Step 2 — Add wings to the facility

1. On the facility's card, click **Add Wing**.
2. Type the wing's **Name** (e.g. *Dog Wing A*) and optional **Operational Notes**, such as what the wing is used for.
3. Click **Create New**.

![Create a wing](screenshots/31b-wing-create.png)

## 5.4 Step 3 — Add cages to the wing

1. On the wing, click **Add Cage**.
2. Fill in:
   - **Code** — a short label for the cage, e.g. *A-01*;
   - **Capacity** — how many animals fit in it;
   - **Species** — the species this cage is for, or *Any species*. When a pet is placed, only cages for its species (or for any species) are offered.
3. Click **Create New**.

![Create a cage](screenshots/31c-cage-create.png)

Repeat for every cage in the wing.

## 5.5 Who is in this cage?

Click the **eye** icon on a cage to list the animals in it. The eye icon next to each animal opens its record.

![Animals in a cage](screenshots/31d-cage-pets.png)

## 5.6 Editing and deleting

- Use the **pencil** icons to rename a facility, wing or cage, or to change a cage's capacity or species.
- Use the **bin** icons to delete them. Deleting a facility also deletes its wings and cages, and deleting a wing also deletes its cages.

> Animals are placed in cages from the pet form (**Cage** field), not from this page. See [Pets](07-pets.md).

## 5.7 Foster families

Many shelters place animals with **foster families** who look after them at home until they are adopted. The application handles them as a special wing, where **each cage is one family**.

1. Create a facility for them, for example *Foster homes* (the address is optional).
2. Add a wing, for example *Foster families*, and switch on **Foster families wing**.

   ![A foster families wing](screenshots/31e-foster-wing-form.png)

3. Add one cage per family. In a foster wing the cage's **Code** field is called **Foster family**: type the family's name (e.g. *Carter family*) and, as **Capacity**, how many animals it can take.
4. Optionally choose a **Contact (volunteer)**: the volunteer record ([chapter 11](11-volunteers.md)) holds the family's phone and address.

   ![A foster family](screenshots/31f-foster-cage-form.png)

To place an animal with a family, choose the family's cage in the pet's **Cage** field, like any other cage. Then:

- the pet's record shows **Foster family** with the family's name and, for managers and staff, the contact's name and a clickable phone number (see [7.3](07-pets.md#73-the-pet-record));
- the contact's volunteer record lists the animals currently with the family;
- foster families are **not counted** in the shelter's capacity on the dashboard, and the Occupancy report counts their animals separately ([13.4](13-reports.md#134-occupancy));
- on the public portal the animal shows an **In a foster family** badge. The family itself is never shown publicly.

Viewers see the family's name, but never its contact.

---

[← Inviting users](04-users-and-invitations.md) · [Documentation index](README.md) · [Next: The dashboard →](06-dashboard.md)
