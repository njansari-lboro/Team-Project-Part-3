# Guidelines for the project

## Source Control with Git and GitHub

### Git

#### Branches
* A branch is a new/separate version of another branch (mostly `main`) in the repository
* They allow you to work on different parts of a project without impacting the `main` branch (usually the live version)
* When the work on a branch is complete, it can be merged with the main project
    * The branch can be deleted after this as it has served its purpose
* You can even switch between branches and work on different sections without them interfering with each other

#### Fetch
* This gets all the change history of a tracked branch

#### Merge
* This combines the current branch with another branch

* Example: merging `login-page` with `main`
    * If the `login-page` branch came directly from `main` and no other changes had been made to `main` while you were working, Git would see this as a continuation of `main`
        * It will then fast-forward and just point both branches to the same commit
        * `main` and `login-page` will essentially both be the same at this point
    * If the `main` branch, however, had changes made to it (such as to `index.html`) then the merge will fail and there would be a conflict between the two versions of `index.html` in each branch
        * Since the merge cannot be done automatically, you will have to fix any conflicts manually
        * If in `index.html` there was a different line of code added in each branch, you would see this in the merge file:
            ```html
            <<<<<<< HEAD
            [line of code added in main]
            =======
            [line of code added in login-page]
            >>>>>>> login-page
            ```
        * You would then need to resolve the conflict which could be combining the two changes like this:
            ```html
            [line of code added in main]
            [line of code added in login-page]
            ```
        * This file can then be staged and committed, provided you definitely fixed the conflict

#### Pull
* This is how you keep up-to-date with all changes, including those made externally
* It is a combination of fetch and merge
    * All changes from the remote repository are pulled (fetched) and merged with the branch you are working on

#### Stage
* This is where you add files that are ready to be committed
* Whenever you hit a milestone or finish a part of the work, you should add the files to the staging environment

#### Commit
* Adding commits keep track of your progress and changes as you work.
* Git considers each commit a change point or "save point"
    * It is a point in the project you can go back to if you find a bug or want to make a change
* When you commit, you **need** to add a message
    * Add clear messages to each commit, so it is easy for yourself (and others) to see what has changed and when

#### Push
* This pushes all your local changes (commits and branches) to GitHub

#### Pull Requests
* This notifies people that you have changes ready for them to consider or review
* They can review your changes or pull your contribution and merge it into their own branch
* Once your code has been "exhaustively tested", it can then be merged with the `main` branch

### GitHub-VS Code Integration

#### Setup
1. Install the `GitHub Repositories` extension
    * Have a read of the features if you want
2. Click on the Remote Explorer icon in the Activity Bar
3. Open a remote repository from GitHub
    * You will have to sign in to GitHub
    * Choose the `lborocs/team-projects-part-2-team-02` repository to open it
4. You should see the repository contents in the Explorer

#### Usage
* Create, edit and delete files freely
* To see all changes, click on the Source Control icon in the Activity Bar
    * You can stage, un-stage and discard changes from here
    * Select a file to open the diff editor
* Everything you do up until now is all local and won't affect the versions on GitHub
* When you're ready to commit the staged changes, enter a commit message and click Commit & Push
    * You may have to sync (pull and push) and fix any conflicts that arise 

* To manage branches, click on the branch button in the status bar (to the right of GitHub)
    * You can switch between branches or create a new one from this menu

### Notes
* **Always create a new branch when making something new (an idea, concept, component, etc.) or proposing a change**
    * Please do not edit the `main` branch directly
    * When we meet up, we will review changes and merge them with the `main` branch (and resolve any conflicts)
    * Hopefully, pushing to the `main` branch will trigger the deployment to the sci-project server
* **Please use descriptive branch names and commit messages**
    * We all need to understand what does what and be able to track our progress
    * It's important to have good commit messages because if we need to revert or reset to a previous commit, we know where to look
    * If a bug is also discovered, we can locate in which commit it has likely been created
* If you are committing multiple files, but you think a single commit message won't cover all the changes, split up the commit
    * Only stage the files you want for each commit so that multiple files with differing changes can be represented by separate commits
* An asterisk next to a branch name indicates that this is the branch you are currently on
    * Or in VS Code it means that the branch has uncommitted changes
* Checking out a branch means moving from one branch to another


## Dialogs

Dialogs are presented using the JavaScript functions `showDialog()` and `showDialogAsync()` in [show-dialog.js](show-dialog.js).

### Best practices

**Use dialogs sparingly.** Dialogs give people important information, but they interrupt the current task to do so. Encourage people to pay attention to your dialogs by making certain that each one offers only essential information and useful actions.

**Avoid using a dialog merely to provide information.** People don't appreciate an interruption from a dialog that's informative, but not actionable. If you need to provide only information, prefer finding an alternative way to communicate it within the relevant context.

**Avoid displaying dialogs for common, undoable actions, even when they're destructive.** For example, you don't need to alert people about data loss every time they delete a file because they do so with the intention of discarding data, and they can undo the action. In comparison, when people take an uncommon destructive action that they can't undo, it's important to display a dialog in case they initiated the action accidentally.

### Content

A dialog is a modal view that displays a title, optional informative text, and up to three buttons.

**In all dialogs, be direct, and use a neutral, approachable tone.** Dialogs often describe problems and serious situations, so avoid being oblique or accusatory, or masking the severity of the issue.

**Write a title that clearly and succinctly describes the situation.** You need to help people quickly understand the situation, so be complete and specific, without being verbose. As much as possible, describe what happened, the context in which it happened, and why. Avoid writing a title that doesn't convey useful information — like "Error" or "Error 201356 occurred" — but also avoid overly long titles that wrap to more than two lines. If the title is a complete sentence, use sentence-style capitalisation and appropriate ending punctuation. If the title is a sentence fragment, use title-style capitalisation, and don't add ending punctuation.

**Include informative text only if it adds value.** If you need to add an informative message, keep it as short as possible, using complete sentences, sentence-style capitalisation, and appropriate punctuation.

**Avoid explaining dialog buttons.** If your dialog text and button titles are clear, you don't need to explain what the buttons do. In rare cases where you need to provide guidance on choosing a button, use a term like "choose" to account for people's current device and interaction method, and refer to a button using its exact title without quotes.

### Buttons

**Create succinct, logical button titles.** Aim for a one- or two-word title that describes the result of selecting the button. Prefer verbs and verb phrases that relate directly to the dialog text — for example, "View All", "Reply", or "Ignore". In informational dialogs only, you can use "OK" for acceptance, avoiding "Yes" and "No". Always use "Cancel" to title a button that cancels the dialog's action. As with all button titles, use title-style capitalisation and no ending punctuation.

**Avoid using OK as the default button title unless the dialog is purely informational.** The meaning of "OK" can be unclear even in dialogs that ask people to confirm that they want to do something. For example, does "OK" mean "OK, I want to complete the action" or "OK, I now understand the negative results my action would have caused"? A specific button title like "Erase", "Convert", "Clear", or "Delete" helps people understand the action they're taking.

**Place buttons where people expect.*** In general, place the button people are most likely to choose on the trailing side in a row of buttons or at the top in a stack of buttons. Always place the default button on the trailing side of a row or at the top of a stack. Cancel buttons are typically on the leading side of a row or at the bottom of a stack.

**Identify destructive buttons.** If a dialog button results in a destructive action, like deleting content, specify the destructive button style to help people recognise it.

**Include a Cancel button when there's a destructive action.*** A Cancel button provides a clear, safe way to avoid a destructive action. Consider making the Cancel button the default button so that people must intentionally choose a button other than the default to continue with the destructive action. Always use the title "Cancel" for a button that cancels a dialog's action.

**\*** Done automatically by the `showDialog()` function, unless overridden.
