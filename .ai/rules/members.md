---
paths:
  - 'app/Livewire/Members/**,resources/views/livewire/members/**'
---

# Members

## Members UI: viewers blocked, managers own deletes and fee defaults
ManageMembers (members.index), MemberForm (members.create/edit) and MemberShow (members.show) are hidden from viewers and admins (personal and financial data, same as sponsorships); the sidebar link sits under @unless isViewerOfCurrentShelter(). Managers and staff create and edit members and record payments (canEditCurrentShelter()). Only managers delete members and edit the shelter's default fees (the "Fees" modal on the list; admins never edit them in ShelterForm). MemberForm pre-fills the fees from the shelter on create; an empty member number means automatic (Member::booted()), and a given one must be unique per shelter, trashed members included. Payment CRUD lives in MemberShow: createPayment('membership_fee') pre-fills Member::nextFeePeriod() and the fee, createPayment('joining_fee') has no period. The "overdue" list filter uses the Member::inArrears() scope. Status and payment method labels use prefixed lang keys (member_active, payment_cash...) because the raw values ('active', 'other', 'left') are too generic.
