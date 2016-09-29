//
//  KidsProfileViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 08/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "KidsProfileViewController.h"
#import "Constants.h"

@interface KidsProfileViewController ()
@end

@implementation KidsProfileViewController
@synthesize dropDown,dict_parentRecord,dict_updateChildRecordData;
- (void)viewDidLoad {
    [super viewDidLoad];
     [self setFontTypeAndFontSize];
    [btn_specialNeedsNo setSelected:YES];
    [btn_medicationNo setSelected:YES];
    [btn_alergisNo setSelected:YES];
    [btn_specialNeed setEnabled:NO];
    txt_dob.enabled = false;
    self.numFormatter = [[NumberFormatter alloc] initWithRegionCode:@"US"];
    [self getRelationShipList];
    [self getSpecialNeed];
    j = self.view.frame.size.width;
    
    [self addImages];
    
    //self.parentInfo = [ApplicationManager getInstance].parentInfo;
    // Add single child
    if (self.checkValue == 1)
    {
        btn_addChild.hidden = true;
        self.navigationItem.title = @"Add Child";
        [btn_relationShip setTitle:@"Parent" forState:UIControlStateNormal];
    }
    // Edit child Value
    if (self.checkValue == 2) {
        btn_addChild.hidden = true;
        self.navigationItem.title = @"Children Profile";
        [self setChildData];
    }
    //DDLogInfo(@"Array is %@",[ApplicationManager getInstance].array_childRecord);
    dict_kidsProfile = [[NSMutableDictionary alloc]init];
    array_ChildProfilePic = [[NSMutableArray alloc]init];
    registrationView =  self.navigationController.viewControllers[1];
    [[self navigationController] setNavigationBarHidden:NO animated:YES];
    
    kAddSaveBarButtonForNavigation;

    //[self.backgroundScrollView removeGestureRecognizer:self.tapRecognizer];
    self.tapRecognizer.cancelsTouchesInView=NO;
    [txtView_helpFullHint.layer setBorderColor:[UIColor grayColor].CGColor];
    [txtView_helpFullHint.layer setBorderWidth:0.5];
    txtView_helpFullHint.clipsToBounds = YES;
    btn_sex.layer.borderColor = [UIColor grayColor].CGColor;
    btn_sex.layer.borderWidth = 0.5;
    btn_relationShip.layer.borderColor = [UIColor grayColor].CGColor;
    btn_relationShip.layer.borderWidth = 0.5;
    self.view.backgroundColor=kBackgroundColor;
//    UITapGestureRecognizer *tapView=[[UITapGestureRecognizer alloc]initWithTarget:self action:@selector(onTouchView:)];
//   [view_mainBG addGestureRecognizer:tapView];
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    
}

- (void)viewDidAppear:(BOOL)animated{
    [super viewDidAppear:animated];
    [self setFontTypeAndFontSize];
    if (imageData==nil && self.checkValue != 2) {
        [view_kidsProfile loadImageFromURL:[NSURL URLWithString:kDefaultImageUrl]];
        //view_kidsProfile.imageView.image = [UIImage imageNamed:@"Default.jpg"];
    }
}
-(void)viewDidLayoutSubviews
{
    [super viewDidLayoutSubviews];
    if (self.checkValue ==1 || self.checkValue == 2) { // if add child button is not available.
        self.backgroundScrollView.frame = CGRectMake(0, 0, self.view.frame.size.width, self.view.frame.size.height);
    }
    // Adjust frame for iPhone 4s
    if (self.view.bounds.size.height <= 568) {
        view_mainBG.frame = CGRectMake(0, 0, 320, 745); // 436 allows 44 for navBar
    }else
    {
        view_mainBG.frame = CGRectMake(0, 0, self.view.bounds.size.width, self.view.frame.size.height);
    }
    
    [self.backgroundScrollView setTranslatesAutoresizingMaskIntoConstraints:NO];
    [self.backgroundScrollView setScrollEnabled:YES];
    //if (![[view_mainBG.subviews lastObject] isMemberOfClass:[NIDropDown class]]) {// Check for last object is not a member of NIDropDown class
        contentHight = 0;
        UIView *lLast = viewChildOtherInfo;//[view_mainBG.subviews lastObject];
        NSInteger wd = lLast.frame.origin.y;
        NSInteger ht = lLast.frame.size.height;
        contentHight = wd+ht;
    //}
    if (![btn_relationShip.titleLabel.text isEqualToString:@"Parent"]&&![btn_relationShip.titleLabel.text isEqualToString:@"Legal guardian"]) {
        [self showChildRelationShipDetailView];
    }else
        [self hideChildRelationShipDetailView];
    self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+10);
}
- (void)touchesEnded:(NSSet<UITouch *> *)touches withEvent:(nullable UIEvent *)event{
    [dropDown hideDropDown:btn_sex];
    [self.dropDownRelationShip hideDropDown:btn_relationShip];
    [self.dropDownSpecialNeed hideDropDown:btn_specialNeed];
    [self dealloc_dropDown];
    //    [self.backgroundScrollView setContentSize:CGSizeMake(320, 750)];
}

- (void)onTouchOnBackground:(UITapGestureRecognizer*)sender {
    [self.view endEditing:YES];
//    if (dropDown!=nil) {
//        [dropDown hideDropDown:btn_sex];
//    }if (self.dropDownRelationShip!=nil) {
//        [self.dropDownRelationShip hideDropDown:btn_relationShip];
//    }if (self.dropDownSpecialNeed!=nil) {
//        [self.dropDownSpecialNeed hideDropDown:btn_specialNeed];
//    }
    //[self.backgroundScrollView setContentOffset:CGPointMake(0, 0)];
}
-(void)onTouchView:(UITapGestureRecognizer*)sender{
    [self.view endEditing:YES];
//    [dropDown hideDropDown:btn_sex];
//    [self.dropDownRelationShip hideDropDown:btn_relationShip];
//    [self.dropDownSpecialNeed hideDropDown:btn_specialNeed];
//    [self dealloc_dropDown];
}
#pragma mark-UserDefineMethods
-(void)setFontTypeAndFontSize
{  // set color
    lbl_sex.textColor = kColorGrayDark;
    lbl_DOB.textColor = kColorGrayDark;
    lbl_name.textColor = kColorGrayDark;
    lbl_specialNeeds.textColor = kColorGrayDark;
    lbl_alergies.textColor = kColorGrayDark;
    lbl_medication.textColor = kColorGrayDark;
    lbl_helpFullHint.textColor = kColorGrayDark;
    lbl_favouriteCartoon.textColor = kColorGrayDark;
    lbl_favouriteBook.textColor = kColorGrayDark;
    lbl_favouriteFood.textColor = kColorGrayDark;
    [btn_specialNeedsNo setTitleColor:kColorGrayDark forState:UIControlStateNormal];
    [btn_specialNeedsYes setTitleColor:kColorGrayDark forState:UIControlStateNormal];
    [btn_specialNeed setTitleColor:kColorGrayDark forState:UIControlStateNormal];
    [btn_specialNeed setTitleColor:kColorGrayDark forState:UIControlStateDisabled];
    [btn_medicationYes setTitleColor:kColorGrayDark forState:UIControlStateNormal];
    [btn_medicationNo setTitleColor:kColorGrayDark forState:UIControlStateNormal];
    [btn_alergisNo setTitleColor:kColorGrayDark forState:UIControlStateNormal];
    [btn_alergisYes setTitleColor:kColorGrayDark forState:UIControlStateNormal];
    [btn_relationShip setTitleColor:kColorGrayDark forState:UIControlStateNormal];
    [btn_sex setTitleColor:kColorGrayDark forState:UIControlStateNormal];
    lbl_hlpHintMessage.textColor = kColorGrayDark;
    lbl_disclaimerText.textColor = kColorGrayDark;
    lbl_ParentName.textColor = kColorGrayDark;
    lbl_parentContact.textColor = kColorGrayDark;
    lbl_RelationShip.textColor = kColorGrayDark;

    // set font type and size
    lbl_sex.font=[UIFont fontWithName:Roboto_Medium size:FontSize12];
    lbl_DOB.font=[UIFont fontWithName:Roboto_Medium size:FontSize12];
    lbl_name.font=[UIFont fontWithName:Roboto_Medium size:FontSize12];
    lbl_specialNeeds.font = [UIFont fontWithName:Roboto_Medium size:FontSize12];
    lbl_alergies.font = [UIFont fontWithName:Roboto_Medium size:FontSize12];
    lbl_medication.font = [UIFont fontWithName:Roboto_Medium size:FontSize12];
    lbl_helpFullHint.font = [UIFont fontWithName:Roboto_Medium size:FontSize12];
    lbl_favouriteFood.font = [UIFont fontWithName:Roboto_Medium size:FontSize12];
    lbl_favouriteCartoon.font = [UIFont fontWithName:Roboto_Medium size:FontSize12];
    lbl_favouriteBook.font = [UIFont fontWithName:Roboto_Medium size:FontSize12];
    lbl_hlpHintMessage.font =  [UIFont fontWithName:Roboto_Medium size:10.0];
    lbl_disclaimerText.font =  [UIFont fontWithName:Roboto_Medium size:10.0];
    lbl_RelationShip.font = [UIFont fontWithName:Roboto_Medium size:FontSize12];
    lbl_ParentName.font = [UIFont fontWithName:Roboto_Medium size:FontSize12];
    lbl_parentContact.font = [UIFont fontWithName:Roboto_Medium size:FontSize12];
    btn_sex.titleLabel.font = [UIFont fontWithName:Roboto_Regular size:FontSize12];
    btn_relationShip.titleLabel.font = [UIFont fontWithName:Roboto_Regular size:FontSize12];
    btn_specialNeed.titleLabel.font = [UIFont fontWithName:Roboto_Regular size:FontSize12];

    
}
-(void)getRelationShipList{
    NSMutableDictionary *d=[[NSMutableDictionary alloc]init];
    [d setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [self startNetworkActivity:NO];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kGetRelationShipListApi];
    networkManager.delegate = self;
    [networkManager relationShipList:d forRequestNumber:2];
}
-(void)getSpecialNeed{
    NSMutableDictionary *d=[[NSMutableDictionary alloc]init];
    [d setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [self startNetworkActivity:NO];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kGetSpecialNeedListApi];
    networkManager.delegate = self;
    [networkManager specialNeedList:d forRequestNumber:3];
}
- (IBAction)onClickAddChild:(id)sender {
   /* [self.view endEditing:YES];
    if ([self checkUserData]) {
        if ([self checkStatus])
        {
            uniqueId= [self generateUUID];
            NSMutableDictionary *dict_ChildProfile = [[NSMutableDictionary alloc]init];
            NSMutableDictionary *dict_child_pic = [[NSMutableDictionary alloc]init];
            [dict_ChildProfile setSafeObject:txt_name.text forKey:kChildName];
            [dict_ChildProfile setSafeObject:txt_dob.text forKey:kChildDOB];
            [dict_ChildProfile setSafeObject:str_alergiesStatus forKey:kchildAlergiesStatus];
            [dict_ChildProfile setSafeObject:txt_alerfies.text forKey:kchildAlergies];
            [dict_ChildProfile setSafeObject:str_medicatorStatus forKey:kChildMedicatorStatus];
            [dict_ChildProfile setSafeObject:txt_medication.text forKey:kChildMedicator];
            [dict_ChildProfile setSafeObject:txt_fevoriteFood.text forKey:kChildFavFood];
            [dict_ChildProfile setSafeObject:txt_favouriteBook.text forKey:kChildFavBook];
            [dict_ChildProfile setSafeObject:txt_favouriteCortoon.text forKey:kChildFavCartoon];
            [dict_ChildProfile setSafeObject:txtView_helpFullHint.text forKey:kChildNotes];
            [dict_ChildProfile setSafeObject:txt_specialNeeds.text forKey:kChildSpecialNeeds];
            [dict_ChildProfile setObject:str_specialNeedsStatus forKey:kChildSpecialNeedsStatus];
            if ([btn_sex.titleLabel.text isEqualToString:@"Male"]) {
                [dict_ChildProfile setSafeObject:@"1" forKey:kChildSex];
            }
            else
                [dict_ChildProfile setSafeObject:@"2" forKey:kChildSex];
            if (imageData==nil) {
                
                view_kidsProfile.imageView.image = [UIImage imageNamed:@"DefaultImage"];
                imageData = UIImageJPEGRepresentation(view_kidsProfile.imageView.image ,1.0);
                
            }
            if (imageData!=nil) {
                [dict_ChildProfile setSafeObject:uniqueId forKey:kChildPic];
                [dict_child_pic setSafeObject:imageData forKey:kPicData];
                [dict_child_pic setSafeObject:uniqueId forKey:@"pictureName"];
                [array_ChildProfilePic addObject:dict_child_pic];
            }
            
            //            [dict_ChildProfile setSafeObject:uniqueId forKey:kChildPic];
            //            [dict_child_pic setSafeObject:imageData forKey:kPicData];
            //            [dict_child_pic setSafeObject:uniqueId forKey:@"pictureName"];
            //            [array_ChildProfilePic addObject:dict_child_pic];
            NSString *str_childCount = [NSString stringWithFormat:@"%d", childCount];
            [dict_kidsProfile setSafeObject:dict_ChildProfile forKey:str_childCount];
            [registrationView.dict_loginData setSafeObject:dict_kidsProfile forKey:kChildProfile];
            DDLogInfo(@"value is %@",registrationView.dict_loginData);
            childCount = childCount+1;
            imageData = nil;
            [view_kidsProfile setImageForCurrentView:[UIImage imageNamed:@"DefaultImage"]];
            // img_kidsProfile.image = nil;
            txt_dob.text = @"";
            txt_favouriteBook.text = @"";
            txt_favouriteCortoon.text = @"";
            txt_fevoriteFood.text = @"";
            txtView_helpFullHint.text = @"";
            txt_name.text = @"";
            txt_specialNeeds.text = @"";
            txt_alerfies.text = @"";
            txt_medication.text = @"";
            [btn_sex setTitle:@"" forState:UIControlStateNormal];
            [btn_alergisYes setSelected:NO];
            [btn_specialNeedsYes setSelected:NO];
            [btn_medicationYes setSelected:NO];
            [self addImages];
        }
        
    }
   */
}

- (IBAction)onClickChooseDOB:(id)sender {
    
    [self.view endEditing:YES];
    [view_dob setFrame:CGRectMake(0,self.view.frame.size.height-280, self.view.frame.size.width, view_dob.frame.size.height)];
    [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,100) animated:YES];
    datePicker.maximumDate=[NSDate date];
    [self.view addSubview:view_dob];
    
}

- (IBAction)onClickDoneDOB:(id)sender {
    NSDateFormatter *format = [[NSDateFormatter alloc] init];
    NSDate *date = datePicker.date;
    [format setDateFormat:@"dd MMM yyyy"];
    NSString *dateString = [format stringFromDate:date];
    datePicker.maximumDate=[NSDate date];
    [view_dob setFrame:CGRectMake(0,570, view_dob.frame.size.width, view_dob.frame.size.height)];
    [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,0) animated:YES];
    txt_dob.text=[NSString stringWithFormat:@" %@",dateString];
    datePicker.date=date;
    [view_dob removeFromSuperview];
    
}
- (IBAction)onClickCancelDOB:(id)sender {
    [view_dob setFrame:CGRectMake(0,570, view_dob.frame.size.width, view_dob.frame.size.height)];
    [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,0) animated:YES];
    [view_dob removeFromSuperview];
    
}
- (IBAction)onClickChoosePhoto:(id)sender {
    if ([UIImagePickerController isSourceTypeAvailable:UIImagePickerControllerSourceTypeCamera]) {
        NSString *other1 = @"Open Camera";
        NSString *other2 = @"Open Photo Album";
        NSString *cancelTitle = @"Cancel";
        UIActionSheet *actionSheet = [[UIActionSheet alloc] initWithTitle:nil delegate:self cancelButtonTitle:cancelTitle destructiveButtonTitle:nil otherButtonTitles:other1, other2, nil];
        [actionSheet showInView:self.view];
    }
    else
    {
        if ([UIImagePickerController isSourceTypeAvailable:UIImagePickerControllerSourceTypeSavedPhotosAlbum])
        {
            UIImagePickerController *imagePicker = [[UIImagePickerController alloc] init];
            
            imagePicker.sourceType =  UIImagePickerControllerSourceTypeSavedPhotosAlbum;
            
            imagePicker.delegate = self;
            
            [imagePicker setAllowsEditing:YES];
            
            [self presentViewController:imagePicker animated:YES completion:nil];
            
        }
    }
}


- (IBAction)onClickSelectSex:(id)sender {
    [self.view endEditing:YES];
    UIButton *btn=(UIButton*)sender;
    NSArray *array_sx = @[@"Male",@"Female"];
    if(dropDown == nil) {
        CGFloat f = 60;
        dropDown = [[NIDropDown alloc]showDropDown:btn :&f :array_sx :nil :@"down"];
        dropDown.delegate = self;
    }
    else {
        [dropDown hideDropDown:btn];
        [self dealloc_dropDown];
    }
    
}

- (IBAction)onClickSelectYesNo:(id)sender {
    UIButton *btn=(UIButton*)sender;
    
    if (btn.tag == 1) { //  Allergies-Selected
        
        [btn_alergisNo setSelected:NO];
        [btn_alergisYes setSelected:YES];
        [txt_alerfies setEnabled:YES];
        str_alergies = txt_alerfies.text;
        str_alergiesStatus = @"Yes";
        [txt_alerfies becomeFirstResponder];
        
    }
    else if(btn.tag == 2)// Allergies not selected
    {
        
        [btn_alergisNo setSelected:YES];
        [btn_alergisYes setSelected:NO];
        [txt_alerfies setEnabled:NO];
        str_alergiesStatus = @"No";
        txt_alerfies.text = @"";
        [txt_alerfies resignFirstResponder];
        
    }
    else if (btn.tag == 3) {// Medication Selected
        [btn_medicationNo setSelected:NO];
        [btn_medicationYes setSelected:YES];
        str_medicatorStatus = @"Yes";
        [txt_medication setEnabled:YES];
        str_medication=txt_medication.text;
        [txt_medication becomeFirstResponder];
    }
    else if(btn.tag == 4)// Medication Not Selected
    {
        [btn_medicationNo setSelected:YES];
        [btn_medicationYes setSelected:NO];
        str_medicatorStatus = @"No";
        txt_medication.text = @"";
        [txt_medication setEnabled:NO];
        str_medication=@"";
        [txt_medication resignFirstResponder];
        
        
    }
    else if (btn.tag == 5) {// Special needs selected
        [btn_specialNeedsNo setSelected:NO];
        [btn_specialNeedsYes setSelected:YES];
        [txt_specialNeeds setEnabled:YES];
        str_specialNeedsStatus = @"Yes";
        str_specialNeeds = txt_specialNeeds.text;
        [btn_specialNeed setEnabled:YES];
        [txt_specialNeeds becomeFirstResponder];
        
    }
    else //3. Special needs not selected
    {
        [btn_specialNeedsNo setSelected:YES];
        [btn_specialNeedsYes setSelected:NO];
        str_specialNeedsStatus = @"No";
        [txt_specialNeeds setEnabled:NO];
        txt_specialNeeds.text = @"";
        [btn_specialNeed setTitle:@"" forState:UIControlStateNormal];
        [btn_specialNeed setEnabled:NO];
        [txt_specialNeeds resignFirstResponder];
    }
}

- (IBAction)onClickRelationShip:(id)sender {
    UIButton *btn=(UIButton*)sender;
    NSMutableArray *array_relation = [[NSMutableArray alloc]init];
    for (NSDictionary *d in self.array_relationShip) {
        [array_relation addObject:[d safeObjectForKey:@"relation_name"]];
    }
    if(self.dropDownRelationShip == nil) {
        CGFloat f = 150;
        self.dropDownRelationShip = [[NIDropDown alloc]showDropDown:btn :&f :array_relation :nil :@"down"];
        self.dropDownRelationShip.delegate = self;
        self.dropDownRelationShip.tag=100;
    }
    else {
        [self.dropDownRelationShip hideDropDown:btn];
        [self dealloc_dropDown];
    }
}

- (IBAction)onClickSpecialNeed:(id)sender {
    UIButton *btn=(UIButton*)sender;
    NSMutableArray *array_specialNeed = [[NSMutableArray alloc]init];
    for (NSDictionary *d in self.array_specialNeed) {
        [array_specialNeed addObject:[d safeObjectForKey:@"special_need"]];
    }
    if(self.dropDownSpecialNeed == nil) {
        CGFloat f = 150;
        self.dropDownSpecialNeed = [[NIDropDown alloc]showDropDown:btn :&f :array_specialNeed :nil :@"down"];
        self.dropDownSpecialNeed.delegate = self;
        self.dropDownSpecialNeed.tag=101;
    }
    else {
        [self.dropDownSpecialNeed hideDropDown:btn];
        [self dealloc_dropDown];
    }
}
-(void)dealloc_dropDown{
    //    [dropDown release];
    dropDown = nil;
    self.dropDownRelationShip=nil;
    self.dropDownSpecialNeed=nil;

}
-(void)showChildRelationShipDetailView{
    [lbl_ParentName setHidden:NO];
    [lbl_parentContact setHidden:NO];
    [txt_parentContact setHidden:NO];
    [txt_parentName setHidden:NO];
    [lbl_disclaimerText setHidden:NO];
    consForChildInfo.constant=150;
    self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+160);

}
-(void)hideChildRelationShipDetailView{
    [lbl_ParentName setHidden:YES];
    [lbl_parentContact setHidden:YES];
    [txt_parentContact setHidden:YES];
    [txt_parentName setHidden:YES];
    [lbl_disclaimerText setHidden:YES];
    consForChildInfo.constant=5;
    self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+10);

}
-(void)updateViewUI:(int)tag{
    DDLogInfo(@"title %@",btn_relationShip.titleLabel.text);
   
        if (![btn_relationShip.titleLabel.text isEqualToString:@"Parent"]&&![btn_relationShip.titleLabel.text isEqualToString:@"Legal guardian"]) {
            [self showChildRelationShipDetailView];
        }else
            [self hideChildRelationShipDetailView];
    [self.view needsUpdateConstraints];

    DDLogInfo(@"frm %@",NSStringFromCGRect(viewChildOtherInfo.frame));
}
#pragma mark-NIDropDownDelegate
- (void) niDropDownDelegateMethod: (NIDropDown *) sender {
    [self performSelector:@selector(updateViewUI:) withObject:nil afterDelay:0.3];
    [self dealloc_dropDown];
}
#pragma mark UIActionSheetDelegate-
- (void)actionSheet:(UIActionSheet *)actionSheet clickedButtonAtIndex:(NSInteger)buttonIndex
{
    [self.navigationController hidesBottomBarWhenPushed];
    if (buttonIndex == 0)
    {
        if ([UIImagePickerController isSourceTypeAvailable:UIImagePickerControllerSourceTypeCamera])
        {
            
            UIImagePickerController *imagePicker = [[UIImagePickerController alloc] init];
            
            imagePicker.sourceType =  UIImagePickerControllerSourceTypeCamera;
            
            imagePicker.delegate = self;
            
            [imagePicker setShowsCameraControls:YES];
            
            [imagePicker setAllowsEditing:YES];
            
            [self presentViewController:imagePicker animated:YES completion:Nil];
            
        }
    }
    else if (buttonIndex == 1)
    {
        
        if ([UIImagePickerController isSourceTypeAvailable:UIImagePickerControllerSourceTypeSavedPhotosAlbum])
        {
            UIImagePickerController *imagePicker = [[UIImagePickerController alloc] init];
            
            imagePicker.sourceType =  UIImagePickerControllerSourceTypeSavedPhotosAlbum;
            
            imagePicker.delegate = self;
            [imagePicker setAllowsEditing:YES];
            [self presentViewController:imagePicker animated:YES completion:Nil];
            
        }
    }
}

// This method is show the image on image view from image picker library.
- (void)imagePickerController:(UIImagePickerController *)picker didFinishPickingMediaWithInfo:(NSDictionary *)info {
    UIImage *image = [info objectForKey:@"UIImagePickerControllerEditedImage"];
    
    UIImage *imgKidsProfile=[self imageWithImage:image scaledToSizeKeepingAspect:CGSizeMake(image.size.width,image.size.height)];
    [view_kidsProfile setImageForCurrentView:imgKidsProfile];
    imageData = UIImageJPEGRepresentation(imgKidsProfile,1);
    
    hasNewImageTaken=YES;
    [picker dismissViewControllerAnimated:YES completion:nil];
}

-(void)onClickedSave:(UIBarButtonItem *)sender{
    [self.view endEditing:YES];
    if ([self checkUserData]) {
        if ([self checkStatus]) {
            if (self.checkValue == 1 || self.checkValue == 2) {
                uniqueId= [self generateUUID];
                NSMutableDictionary *dict_ChildProfile = [[NSMutableDictionary alloc]init];
               [dict_ChildProfile setSafeObject:self.sitterInfo.str_TokenData forKey:kToken];
                [dict_ChildProfile setSafeObject:self.sitterInfo.sitterId forKey:kUserId];
                if (self.checkValue == 2) {
                   // [dict_ChildProfile setSafeObject:self.childrenInfo.childJobId forKey:kChildId];
                }
                if (imageData==nil) {
                    // view_kidsProfile.imageView.image = [UIImage imageNamed:@"DefaultImage"];
                    imageData = UIImageJPEGRepresentation(view_kidsProfile.imageView.image ,1.0);
                }
                //[dict_ChildProfile setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
                [dict_ChildProfile setSafeObject:txt_name.text forKey:kChildName];
                [dict_ChildProfile setSafeObject:txt_dob.text forKey:kDOB];
                [dict_ChildProfile setSafeObject:str_alergiesStatus forKey:kchildAlergiesStatus];
                [dict_ChildProfile setSafeObject:txt_alerfies.text forKey:kchildAlergies];
                [dict_ChildProfile setSafeObject:str_medicatorStatus forKey:kChildMedicatorStatus];
                [dict_ChildProfile setSafeObject:txt_medication.text forKey:kChildMedicator];
                [dict_ChildProfile setSafeObject:txt_fevoriteFood.text forKey:kChildFavFood];
                [dict_ChildProfile setSafeObject:txt_favouriteBook.text forKey:kChildFavBook];
                [dict_ChildProfile setSafeObject:txt_favouriteCortoon.text forKey:kChildFavCartoon];
                [dict_ChildProfile setSafeObject:txtView_helpFullHint.text forKey:kChildNotes];
                //[dict_ChildProfile setSafeObject:btn_specialNeed.titleLabel.text forKey:kChildSpecialNeeds];
                [dict_ChildProfile setSafeObject:txt_specialNeeds.text forKey:kChildSpecialNeeds];
                [dict_ChildProfile setObject:str_specialNeedsStatus forKey:kChildSpecialNeedsStatus];
                [dict_ChildProfile setSafeObject:btn_relationShip.titleLabel.text forKey:kChildRelation];
                if (![btn_relationShip.titleLabel.text isEqualToString:@"Parent"]&&![btn_relationShip.titleLabel.text isEqualToString:@"Legal guardian"]) {
                    [dict_ChildProfile setSafeObject:txt_parentName.text forKey:kRealtionName];
                    [dict_ChildProfile setSafeObject:[self.numFormatter rawText:txt_parentContact.text] forKey:kRealtionContact];
                }
                [dict_ChildProfile setSafeObject:uniqueId forKey:kChildPic];
                if ([btn_sex.titleLabel.text isEqualToString:@"Male"]) {
                    [dict_ChildProfile setSafeObject:@"1" forKey:kChildSex];
                }
                else
                    [dict_ChildProfile setSafeObject:@"2" forKey:kChildSex];
                [dict_ChildProfile setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
                [dict_ChildProfile setSafeObject:self.jobId forKey:kJobId];
                
                [self startNetworkActivity:NO];
                SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kAddChildApi];
                DDLogInfo(@"%@",kAddChildApi);
                networkManager.delegate = self;
                [networkManager AddChild:dict_ChildProfile forRequestNumber:1 images:imageData];
            }
        }
        
    }
}
-(BOOL)checkUserData
{   BOOL isvalid = YES;
    NSString *str_sex = btn_sex.titleLabel.text;
    
    //    if (imageData == nil) {
    //        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kSelectProfilePic];
    //        isvalid = NO;
    //    }
    if ([txt_name.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterName];
        isvalid = NO;
    }
    else if ([str_sex isEqualToString:@""] || str_sex == nil)
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kSelectSex];
        isvalid = NO;
    }
    else if ([txt_dob.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kSelectDOB];
        isvalid = NO;
    }
    else if (![btn_relationShip.titleLabel.text isEqualToString:@"Parent"]&&![btn_relationShip.titleLabel.text isEqualToString:@"Legal guardian"]) {
        if ([txt_parentName.text isEqualToString:@""])
        {
            [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:@"Please enter parent name."];
            isvalid = NO;
        }else if ([txt_parentContact.text isEqualToString:@""])
        {
            [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:@"Please enter parent Contact number."];
            isvalid = NO;
        }
    }
    else
        isvalid = YES;
    
    return isvalid;
}
-(BOOL)checkStatus
{
    BOOL isvalid = YES;
    
    DDLogInfo(@"specical nee title %@",btn_specialNeed.titleLabel.text);
    if ([str_specialNeedsStatus isEqualToString:@"Yes"]&&([txt_specialNeeds.text isEqualToString:@""]||(txt_specialNeeds.text==nil))) {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterSpecialNeeds];
        isvalid = NO;
    }
    else if ([str_alergiesStatus isEqualToString:@"Yes"]&&[txt_alerfies.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterAlergies];
        isvalid = NO;
    }
    else if ([str_medicatorStatus isEqualToString:@"Yes"]&&[txt_medication.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterMedication];
        isvalid = NO;
    }
    else
    {
        isvalid = YES;
    }
    return isvalid;
    
}
#define mark TextField/Textview delegates
- (void)textViewDidBeginEditing:(UITextView *)textView {
    if (txt_parentName.hidden) {
        [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,textView.frame.origin.y) animated:YES];
    }else{
        [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,textView.frame.origin.y+160) animated:YES];
    }
    
}
- (void)textViewDidEndEditing:(UITextView *)textView{
    
}
- (BOOL)textViewShouldBeginEditing:(UITextView *)textView{
    return YES;
}
- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string{
    if (textField==txt_parentContact ) {
        textField.text=[self.numFormatter formatText:textField.text];
    }
    return YES;
}
//- (void)textFieldDidEndEditing:(UITextField *)textField{
//[self setActiveTextField:textField];
//}
- (BOOL)textFieldShouldReturn:(UITextField *)textField{
    //[self.view endEditing:YES];
    if (textField!=txt_favouriteBook) {
        [self setActiveTextField:textField];
        return NO;
    }else{
        return [textField resignFirstResponder];
    }
}
-(void)setActiveTextField:(UITextField*)txtField
{
    if (txtField==txt_parentName) {
        [txt_parentContact becomeFirstResponder];
    }
    else if (txtField==txt_name) {
        if (txt_alerfies.enabled) {
            [txt_alerfies becomeFirstResponder];
        }else if (txt_medication.enabled) {
            [txt_medication becomeFirstResponder];
        }else if (txt_specialNeeds.enabled) {
            [txt_specialNeeds becomeFirstResponder];
        }
        else {
            [txtView_helpFullHint becomeFirstResponder];
        }
    }else if (txtField==txt_alerfies) {
        if (txt_medication.enabled) {
            [txt_medication becomeFirstResponder];
        }else if (txt_specialNeeds.enabled) {
            [txt_specialNeeds becomeFirstResponder];
        }
        else {
            [txtView_helpFullHint becomeFirstResponder];
        }
    }else if (txtField==txt_medication) {
         if (txt_specialNeeds.enabled) {
            [txt_specialNeeds becomeFirstResponder];
        }else{
            [txtView_helpFullHint becomeFirstResponder];
        }
    }
    else if (txtField==txt_specialNeeds) {
        [txtView_helpFullHint becomeFirstResponder];
        
    }else if (txtField==txt_fevoriteFood) {
        [txt_favouriteCortoon becomeFirstResponder];
    }else if (txtField==txt_favouriteCortoon) {
        [txt_favouriteBook becomeFirstResponder];
    }else if (txtField==txt_favouriteCortoon) {
        [txt_favouriteBook becomeFirstResponder];
    }else if (txtField==txt_favouriteBook) {
        [txt_favouriteBook resignFirstResponder];
    }
}
-(void)addImages
{
    [txt_alerfies setEnabled:NO];
    [txt_specialNeeds setEnabled:NO];
    [txt_medication setEnabled:NO];
    [btn_alergisNo setSelected:YES];
    [btn_specialNeedsNo setSelected:YES];
    [btn_medicationNo setSelected:YES];
    str_alergiesStatus = @"No";
    str_medicatorStatus = @"No";
    str_specialNeedsStatus = @"No";
    [btn_alergisNo setImage:[UIImage imageNamed:@"radiochk-2"] forState:UIControlStateSelected];
    [btn_alergisNo setImage:[UIImage imageNamed:@"radio"] forState:UIControlStateNormal];
    [btn_specialNeedsNo setImage:[UIImage imageNamed:@"radiochk-2"] forState:UIControlStateSelected];
    [btn_specialNeedsNo setImage:[UIImage imageNamed:@"radio"] forState:UIControlStateNormal];
    [btn_medicationNo setImage:[UIImage imageNamed:@"radiochk-2"] forState:UIControlStateSelected];
    [btn_medicationNo setImage:[UIImage imageNamed:@"radio"] forState:UIControlStateNormal];
    [btn_specialNeedsYes setImage:[UIImage imageNamed:@"radiochk-2"] forState:UIControlStateSelected];
    [btn_specialNeedsYes setImage:[UIImage imageNamed:@"radio"] forState:UIControlStateNormal];
    [btn_alergisYes setImage:[UIImage imageNamed:@"radiochk-2"] forState:UIControlStateSelected];
    [btn_alergisYes setImage:[UIImage imageNamed:@"radio"] forState:UIControlStateNormal];
    [btn_medicationYes setImage:[UIImage imageNamed:@"radiochk-2"] forState:UIControlStateSelected];
    [btn_medicationYes setImage:[UIImage imageNamed:@"radio"] forState:UIControlStateNormal];
    
}
-(void)setChildData
{
    DDLogInfo(@"child detail %@",self.childrenInfo);
    txt_alerfies.text = self.childrenInfo.childallergies;
    txt_dob.text = self.childrenInfo.childdob;
    txt_favouriteBook.text = self.childrenInfo.childFavbook;
    txt_favouriteCortoon.text = self.childrenInfo.childFavCartoon;
    txt_fevoriteFood.text = self.childrenInfo.childfavFood;
    txt_name.text = self.childrenInfo.childName;
    txtView_helpFullHint.text = self.childrenInfo.childHelpFullHint;
    txt_specialNeeds.text=self.childrenInfo.childSpecialNeeds;
//    [btn_specialNeed setTitle:self.childrenInfo.childSpecialNeeds forState:UIControlStateDisabled];
//    [btn_specialNeed setTitle:self.childrenInfo.childSpecialNeeds forState:UIControlStateNormal];
    
    [btn_relationShip setTitle:self.childrenInfo.childRelationShip forState:UIControlStateDisabled];
    [btn_relationShip setTitle:self.childrenInfo.childRelationShip forState:UIControlStateNormal];
    if([self.childrenInfo.childRelationShip isEqualToString:@""]||self.childrenInfo.childRelationShip==nil)
    {
        [btn_relationShip setTitle:@"Parent" forState:UIControlStateNormal];
    }
    if (![btn_relationShip.titleLabel.text isEqualToString:@"Parent"]&&![btn_relationShip.titleLabel.text isEqualToString:@"Legal guardian"]) {
        txt_parentName.text = self.childrenInfo.childParentName;
        txt_parentContact.text =[self.numFormatter formatText:self.childrenInfo.childParentContact];
        [self showChildRelationShipDetailView];
    }else{
        txt_parentName.text = @"";
        txt_parentContact.text = @"";
        [self hideChildRelationShipDetailView];
    }
    
    
    
    
    txt_medication.text = self.childrenInfo.childMedicator;
    NSURL *img_url=[NSURL URLWithString:self.childrenInfo.childThumbImage];
    [view_kidsProfile loadImageFromURL:img_url];
    if ([self.childrenInfo.childSex isEqualToString:@"M"]) {
        [btn_sex setTitle:@"Male" forState:UIControlStateNormal];
    }
    else
        [btn_sex setTitle:@"Female" forState:UIControlStateNormal];
    if ([self.childrenInfo.childSpecialNeedsStatus isEqualToString:@"Yes"]) {
        [btn_specialNeedsNo setSelected:NO];
        [btn_specialNeedsYes setSelected:YES];
        [txt_specialNeeds setEnabled:YES];
        [btn_specialNeed setEnabled:YES];
        str_specialNeedsStatus = @"Yes";
        str_specialNeeds = txt_specialNeeds.text;
    }
    else
    {
        [btn_specialNeedsNo setSelected:YES];
        [btn_specialNeedsYes setSelected:NO];
        str_specialNeedsStatus = @"No";
        [btn_specialNeed setEnabled:NO];
        [txt_specialNeeds setEnabled:NO];
    }
    if ([self.childrenInfo.childAllergyStatus isEqualToString:@"Yes"]) {
        [btn_alergisNo setSelected:NO];
        [btn_alergisYes setSelected:YES];
        [txt_alerfies setEnabled:YES];
        str_alergies = txt_alerfies.text;
        str_alergiesStatus = @"Yes";
    }
    else{
        [btn_alergisNo setSelected:YES];
        [btn_alergisYes setSelected:NO];
        [txt_alerfies setEnabled:NO];
        str_alergiesStatus = @"No";
        
        
    }
    if ([self.childrenInfo.childMedicatorStatus isEqualToString:@"Yes"]) {
        [btn_medicationNo setSelected:NO];
        [btn_medicationYes setSelected:YES];
        [txt_medication setEnabled:YES];
        str_medication = txt_medication.text;
        str_medicatorStatus = @"Yes";
        
    }
    else{
        [btn_medicationNo setSelected:YES];
        [btn_medicationYes setSelected:NO];
        str_medicatorStatus = @"No";
        [txt_medication setEnabled:NO];
        
    }
   // DDLogInfo(@"title %@",btn_specialNeed.titleLabel.text);

}
/**
 This method is called for generating unique id string
 @param none
 @returns NSString
 */
- (NSString *)generateUUID {
    CFUUIDRef theUUID = CFUUIDCreate(NULL);
    CFStringRef string = CFUUIDCreateString(NULL, theUUID);
    CFRelease(theUUID);
    return (__bridge  NSString *)string;
}
#pragma mark - SMCoreNetworkManagerDelegate

- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    DDLogInfo(@"%@",responseObject);
    switch (requestId) {
        case 1:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                NSDictionary *dictChildDetail=[[dict_responseObj safeObjectForKey:kData] safeObjectForKey:@"child_detail"];
                NSString *childId=[dictChildDetail safeObjectForKey:@"child_id"];
                [[NSNotificationCenter defaultCenter] postNotificationName:kAddChildNotification object:childId];
                [self.navigationController popViewControllerAnimated:YES];
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            break;
        case 2:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                self.array_relationShip=[[NSArray alloc] initWithArray:[dict_responseObj safeObjectForKey:kData]];
                if (self.checkValue == 2) {
                    [self setChildData];
                }
                
            }
            break;
        case 3:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                self.array_specialNeed=[[NSArray alloc] initWithArray:[dict_responseObj safeObjectForKey:kData]];
                
            }
            break;
            default:
            break;
            
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}
- (void)receiveEvent:(NSNotification *)notification {
   /* NSMutableDictionary *dict_kidsData=[[NSMutableDictionary alloc] init];
    [dict_kidsData setSafeObject:self.parentInfo.tokenData forKey:kToken];
    [dict_kidsData setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
    [dict_kidsData setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [self startNetworkActivity:NO];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kChildrenListApi];
    DDLogInfo(@"%@",kChildrenListApi);
    networkManager.delegate = self;
    [networkManager childrenList:dict_kidsProfile forRequestNumber:4];*/
    
}
/**
 This methos is used for adjusting image size
 @param id image,newSize
 @return UIImage object
 */
- (UIImage*)imageWithImage:(UIImage*)image scaledToSizeKeepingAspect:(CGSize)newSize
{
    // Create a graphics image context
    //    if (image.size.width<302 && image.size.height<194) {
    if (image.size.width<301 && image.size.height<461)
    {
        return image;
    }
    float imgHeight = image.size.height;
    float imgWeight = image.size.width;
    float fact = 0.0,fact1,fact2;
    fact1 = imgHeight/newSize.height;
    fact2 = imgWeight/newSize.width;
    if (fact1>fact2)
    {
        fact=fact1;
    }
    else
        fact=fact2;
    imgHeight = imgHeight / fact;
    imgWeight = imgWeight / fact;
    newSize  = CGSizeMake(imgWeight, imgHeight);
    UIGraphicsBeginImageContext(newSize);
    
    // Tell the old image to draw in this new context, with the desired
    // new size
    [image drawInRect:CGRectMake(0,0,newSize.width,newSize.height)];
    
    // Get the new image from the context
    UIImage* newImage = UIGraphicsGetImageFromCurrentImageContext();
    
    // End the context
    UIGraphicsEndImageContext();
    
    // Return the new image.
    return newImage;
}
@end
