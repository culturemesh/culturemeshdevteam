culturemeshdevteam
---------------------------

Say! If you wanna contribute to stuff and things, follow this handy guide here!

|||||||||||||||||||||||||||||||||||||
||| FROM TERMINAL
---------------------------------

a) SETUP GITHUB
-------------

1) go to some awesome, empty directory

2) say git clone https://github.com/culturemesh/culturemeshdevteam.git

That will clone the master branch into the directory that you've navigated to. WARNING!!! You don't want to work on the master branch, you want to create your own branch each time

-----------------------------------------------------------------------------

b) EDITING CODE
-------------

1) Create a branch for the new feature with

git checkout -b <new branch name>

	e.g. - git checkout new_thing

2) Edit it!!!!

	- You save changes to your local repo by using the commit command
	- To check the status of your next commit, say:
	
		git status
	
	- To add files to the commit, say:

		git add <filename>
		
		eg - git add file.txt
		
			OR
			
		To add all files say
		
		git add .
		
	- To remove files from commit, say
	
		git rm <filename>
		
		eg - git rm file.txt

3) Commit your changes by saying:

	git commit -m "message within quotes"


You can commit your changes
-----------------------------------------------------------------------------

c) CODE REVIEW
------------

1) To push your changes onto the host repo, say:

	git push origin <branch_name>

	e.g. - git push origin new_thing
	
		-- WARNING --
		
		Make sure you don't push 
		to the master branch by
		accident!

2) Navigate to the main repo at github.com/culturemesh/culturemeshdevteam

3) Switch the context to the branch that you just created

4) Press the pull request button to begin the code review process

5) The rest of the team will review the code. If we suggest changes, you can make the edits on your local machine and push them into the repo. 

**** Alternatively, someone on the team could pull the branch from the repo and make changes themselves. ****

6) Once all the changes have been approved, a team member will merge the branch into the master branch and the whole process begins all over again!


