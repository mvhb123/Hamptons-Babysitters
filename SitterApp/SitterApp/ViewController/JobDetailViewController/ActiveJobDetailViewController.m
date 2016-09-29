//
//  ActiveJobDetsilViewController.m
//  SitterApp
//
//  Created by Vikram gour on 16/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "ActiveJobDetailViewController.h"

@interface ActiveJobDetailViewController ()

@end

@implementation ActiveJobDetailViewController
@synthesize indexPath,jobTypeFlag,sitterInfo;

- (void)viewDidLoad {
    [super viewDidLoad];

    // Do any additional setup after loading the view from its nib.
    self.jobList=[ApplicationManager getInstance].jobList;
//    if (self.jobTypeFlag==2) {
//        dict_ChildData=[self.jobList.array_ScheduledJob safeObjectAtIndex:indexPath];
//    }else{
//        dict_ChildData=[self.jobList.array_ActiveJob safeObjectAtIndex:indexPath];
//    }
    self.sitterInfo=[ApplicationManager getInstance].sitterInfo;
    pageNo=0;
    [btn_next setHidden:YES];
    [btn_previous setHidden:YES];
    [self.backgroundScrollView setHidden:YES];
    self.view.backgroundColor=kBackgroundColor;
    view_bottom.backgroundColor=kBackgroundColor;
//     [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(updateChildCount) name:kUpdateChildCount object:nil];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    if (self.jobTypeFlag==2) {
        self.navigationItem.title=@"Scheduled Jobs Details";
    }else{
        self.navigationItem.title=@"Active Job Details";
    }
    [self setTapGestureForPhone];
    if (self.jobTypeFlag==2) {
        dict_ChildData=[self.jobList.array_ScheduledJob safeObjectAtIndex:indexPath];
    }else{
        dict_ChildData=[self.jobList.array_ActiveJob safeObjectAtIndex:indexPath];
    }

}
-(void)viewDidAppear:(BOOL)animated{
    [super viewDidAppear:animated];
    
    self.view.backgroundColor=kBackgroundColor;
    self.backgroundScrollView.backgroundColor=kBackgroundColor;
    view_jobDetail.backgroundColor=kBackgroundColor;
   view_parentDetail.backgroundColor=kBackgroundColor;
   view_otherContactDetail.backgroundColor=kBackgroundColor;
    view_specialNotes.backgroundColor=kBackgroundColor;
    [lbl_specialNotesValue setVerticalAlignment:VerticalAlignmentTop];
    [self setFontAndColrForView];
   // [self showChildDetail];
    [self.backgroundScrollView setHidden:NO];
    if (self.jobId!=0) {
        [self performSelector:@selector(startNetworkActivity:) withObject:nil afterDelay:0.003];
        [self getJobDetail:self.jobId];
    }else{
        [self showChildDetail];
    }
   }
-(void)viewWillDisappear:(BOOL)animated{
    [super viewWillDisappear:animated];
    [self.view endEditing:YES];
    //[self.backgroundScrollView setContentOffset:CGPointMake(0, 0) animated:NO];
}
-(void)viewDidLayoutSubviews
{
    [super viewDidLayoutSubviews];
    //slide the view in
    if ([[[dict_ChildData safeObjectForKey:kJobStatus] lowercaseString] isEqualToString:@"inactive"]) {
        view_parentDetail.hidden=YES;
        view_otherContactDetail.hidden=YES;
        [view_specialNotes setFrame:CGRectMake(view_specialNotes.frame.origin.x,view_otherContactDetail.frame.origin.y, view_specialNotes.frame.size.width, view_specialNotes.frame.size.height)];
    }
    self.backgroundScrollView.frame = CGRectMake(0, self.backgroundScrollView.frame.origin.y, self.view.frame.size.width, self.view.frame.size.height);
    [lbl_emergencyContactName sizeToFit];
    [view_specialNotes setBackgroundColor:[UIColor clearColor]];
}
#pragma mark-UserDefineMethods
-(void)setFontAndColrForView{
  
    //Heading label
    [lbl_jobNumber setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_startDate setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_endDate setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_childCount setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_Address setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_parentName setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_parentPhone1 setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_parentPhone2 setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_ParentEmail setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_guardianName setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_guardianPhone1 setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_guardianPhone2 setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_emergencyContactName setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_relationship setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_emergencyContactPhone1 setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_emContactName setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];

    [lbl_specialNotes setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_area setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];

    //Value label
     [lbl_jobNumberValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_startDateValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_endDateValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_childCountValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_AddressValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_parentNameValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_parentPhone1Value setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_parentPhone2Value setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_ParentEmailValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_guardianNameValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_guardianPhone1Value setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_guardianPhone2Value setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_emergencyContactNameValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_relationshipValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_emergencyContactPhone1Value setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_specialNotesValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_areaValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_headingChildDetail setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];

    
    [lbl_jobNumberValue setTextColor:kColorGrayDark];
    [lbl_startDateValue setTextColor:kColorGrayDark];
    [lbl_endDateValue setTextColor:kColorGrayDark];
    [lbl_childCountValue setTextColor:kColorGrayDark];
    [lbl_AddressValue setTextColor:kColorGrayDark];
    [lbl_parentNameValue setTextColor:kColorGrayDark];
    [lbl_parentPhone1Value setTextColor:kColorGrayDark];
    [lbl_parentPhone2Value setTextColor:kColorGrayDark];
    [lbl_ParentEmailValue setTextColor:kColorGrayDark];
    [lbl_guardianNameValue setTextColor:kColorGrayDark];
    [lbl_guardianPhone1Value setTextColor:kColorGrayDark];
    [lbl_guardianPhone2Value setTextColor:kColorGrayDark];
    [lbl_emergencyContactNameValue setTextColor:kColorGrayDark];
    [lbl_relationshipValue setTextColor:kColorGrayDark];
    [lbl_emergencyContactPhone1Value setTextColor:kColorGrayDark];
    [lbl_specialNotesValue setTextColor:kColorGrayDark];
    [lbl_emContactName setTextColor:kColorGrayDark];
    
    [lbl_jobNumber setTextColor:kColorGrayDark];
    [lbl_startDate setTextColor:kColorGrayDark];
    [lbl_endDate setTextColor:kColorGrayDark];
    [lbl_childCount setTextColor:kColorGrayDark];
    [lbl_Address setTextColor:kColorGrayDark];
    [lbl_parentName setTextColor:kColorGrayDark];
    [lbl_parentPhone1 setTextColor:kColorGrayDark];
    [lbl_parentPhone2 setTextColor:kColorGrayDark];
    [lbl_ParentEmail setTextColor:kColorGrayDark];
    [lbl_guardianName setTextColor:kColorGrayDark];
    [lbl_guardianPhone1 setTextColor:kColorGrayDark];
    [lbl_guardianPhone2 setTextColor:kColorGrayDark];
    [lbl_area setTextColor:kColorGrayDark];
    [lbl_relationship setTextColor:kColorGrayDark];
    [lbl_emergencyContactPhone1 setTextColor:kColorGrayDark];
    [lbl_areaValue setTextColor:kColorGrayDark];
    //[lbl_specialNotesValue setContentInset:UIEdgeInsetsMake(-8, 0, 0, 0)];

    
}
-(void)showChildDetail
{
    DDLogInfo(@"dict_ChildData %@",dict_ChildData);
    //Job detail
    lbl_jobNumberValue.text=[dict_ChildData safeObjectForKey:kJobId];
    lbl_startDateValue.text=[dict_ChildData safeObjectForKey:kJobStartDate];
    lbl_endDateValue.text=[dict_ChildData safeObjectForKey:kJobEndDate];
    lbl_childCountValue.text=[dict_ChildData safeObjectForKey:kActual_child_count];
    NSDictionary *dict_Address=[dict_ChildData safeObjectForKey:kAddress];
   
    NSString *strAddress=[NSString stringWithFormat:@""];
    strAddress= [strAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[dict_Address safeObjectForKey:kStreetAddress]]];
    strAddress= [strAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[dict_Address safeObjectForKey:kCity]]];
    strAddress=[strAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[dict_Address safeObjectForKey:kState]]];
    strAddress=[strAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[dict_Address safeObjectForKey:kZipCode]]];
    if (![[dict_Address safeObjectForKey:kAddress1] isEqualToString:@""]) {
        strAddress=[strAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[dict_Address safeObjectForKey:kAddress1]]];
    }
    if (![[dict_Address safeObjectForKey:kHotelName] isEqualToString:@""]) {
        strAddress=[strAddress stringByAppendingString:[NSString stringWithFormat:@"%@",[dict_Address safeObjectForKey:kHotelName]]];
    }
    lbl_AddressValue.text=strAddress;
    [lbl_AddressValue setVerticalAlignment:VerticalAlignmentTop];
    [lbl_AddressValue setNumberOfLines:3];
    [lbl_AddressValue setLineBreakMode:NSLineBreakByTruncatingTail];
    [lbl_AddressValue sizeToFit];
    [lbl_AddressValue needsUpdateConstraints];
    lbl_areaValue.text=[NSString stringWithFormat:@"%@",[dict_Address safeObjectForKey:kCity]];
    if ([[[dict_ChildData safeObjectForKey:kJobStatus] lowercaseString] isEqualToString:@"inactive"]) {
        view_parentDetail.hidden=YES;
        view_otherContactDetail.hidden=YES;
       [view_specialNotes setFrame:CGRectMake(view_specialNotes.frame.origin.x,view_otherContactDetail.frame.origin.y, view_specialNotes.frame.size.width, view_specialNotes.frame.size.height)];
    }else{
    //Parent detail
        view_parentDetail.hidden=NO;
        view_otherContactDetail.hidden=NO;

    lbl_parentNameValue.text=[NSString stringWithFormat:@"%@ %@",[[dict_ChildData safeObjectForKey:kParentInfo] safeObjectForKey:kFirstName],[[dict_ChildData safeObjectForKey:kParentInfo] safeObjectForKey:kLastName]];
    lbl_parentPhone1Value.text=[self.numFormatter formatText:[NSString stringWithFormat:@"%@",[[dict_ChildData safeObjectForKey:kParentInfo] safeObjectForKey:kPhone]]];
        if ([[[dict_ChildData safeObjectForKey:kParentInfo] safeObjectForKey:kLocal_Phone] isEqualToString:@""]) {
            lbl_parentPhone2Value.text=@"NA";
        }else{
        lbl_parentPhone2Value.text=[self.numFormatter formatText:[NSString stringWithFormat:@"%@",[[dict_ChildData safeObjectForKey:kParentInfo] safeObjectForKey:kLocal_Phone]]];
        }
    
    lbl_ParentEmailValue.text=[NSString stringWithFormat:@"%@",[[dict_ChildData safeObjectForKey:kParentInfo] safeObjectForKey:kUserName]];
    }
    // Child detail
    arrChildData=[dict_ChildData objectForKey:@"children"];
    if (arrChildData.count<=1) {
        [btn_next setHidden:YES];
    }else{
        [btn_next setHidden:NO];
    }
     [btn_previous setHidden:YES];
    int viewContent=0;
    [scroll_View setPagingEnabled:YES];
    for (int i=0;i<arrChildData.count;i++) {
        NSDictionary *d=[arrChildData objectAtIndex:i];
        UIView *v=[[ApplicationManager getInstance] createViewForchildInfo:d frame:CGRectMake(viewContent+(scroll_View.frame.size.width*i), 0, scroll_View.frame.size.width, scroll_View.frame.size.height)];
       
        [scroll_View addSubview:v];
        totalCount=i;

    }
    [scroll_View setContentSize:CGSizeMake((scroll_View.frame.size.width*arrChildData.count)+viewContent, scroll_View.frame.size.height)];
    
    // Other contact detail
    if ([[dict_ChildData safeObjectForKey:kGuardianName] isEqualToString:@""]) {
        lbl_guardianNameValue.hidden=YES;
        lbl_guardianPhone1Value.hidden=YES;
        lbl_guardianPhone2Value.hidden=YES;
        lbl_guardianName.hidden=YES;
        lbl_guardianPhone1.hidden=YES;
        lbl_guardianPhone2.hidden=YES;
        consForEmergencyContact.constant=-70;
        consForSpecialNeed.constant=-80;
//        CGRect vfrm=view_otherContactDetail.frame;
//        vfrm.size.height=113;
//        [view_otherContactDetail setFrame:vfrm];
//        consForContactHeight.constant=113;
    }else{
        lbl_guardianNameValue.hidden=NO;
        lbl_guardianPhone1Value.hidden=NO;
        lbl_guardianPhone2Value.hidden=NO;
        lbl_guardianNameValue.text=[NSString stringWithFormat:@"%@",[dict_ChildData safeObjectForKey:kGuardianName]];
        lbl_guardianPhone1Value.text=[self.numFormatter formatText:[NSString stringWithFormat:@"%@",[dict_ChildData safeObjectForKey:kGuardianPhone1]]];
        if ([[dict_ChildData safeObjectForKey:kGuardianPhone2] isEqualToString:@""]) {
            lbl_guardianPhone2Value.text=@"NA";
        }else{
        lbl_guardianPhone2Value.text=[self.numFormatter formatText:[NSString stringWithFormat:@"%@",[dict_ChildData safeObjectForKey:kGuardianPhone2]]];
        }
    }
    
    lbl_emergencyContactNameValue.text=[NSString stringWithFormat:@"%@",[dict_ChildData safeObjectForKey:kEmergencyContact]];
    lbl_emergencyContactPhone1Value.text=[self.numFormatter formatText:[NSString stringWithFormat:@"%@",[dict_ChildData safeObjectForKey:kEmergencyPhone]]];
    lbl_relationshipValue.text=[NSString stringWithFormat:@"%@",[dict_ChildData safeObjectForKey:kRelationship]];
    
    if ([[dict_ChildData safeObjectForKey:kJobSpecialNeed] isEqualToString:@""]) {
        [lbl_specialNotesValue setHidden:YES];
       [lbl_specialNotes setHidden:YES];
        CGRect frm=btn_modifyJob.frame;
        frm.origin.y=lbl_specialNotes.frame.origin.y;
        consForcancelbtn.constant=-50;
        consForSpecialNeedHeight.constant=46;
        consForContactHeight.constant=113;
        if (![[dict_ChildData safeObjectForKey:kNote_aboutJob] isEqualToString:@""]){
            UILabel *lblSpecialInstruction=[[UILabel alloc]initWithFrame:CGRectMake(lbl_specialNotes.frame.origin.x, lbl_specialNotes.frame.origin.y,150, lbl_specialNotes.frame.size.height)];
            [lblSpecialInstruction setTextColor:lbl_specialNotes.textColor];
            [lblSpecialInstruction setFont:lbl_specialNotes.font];
            VerticallyAlignedLabel *lblSpecialInstructionValue=[[VerticallyAlignedLabel alloc]initWithFrame:CGRectMake(lbl_specialNotes.frame.origin.x,lblSpecialInstruction.frame.size.height+lblSpecialInstruction.frame.origin.y,lbl_specialNotesValue.frame.size.width,lbl_specialNotesValue.frame.size.height)];
            [lblSpecialInstructionValue setVerticalAlignment:VerticalAlignmentTop];
            [lblSpecialInstructionValue setBackgroundColor:[UIColor clearColor]];
            [lblSpecialInstructionValue setTextColor:lbl_specialNotesValue.textColor];
            [lblSpecialInstructionValue setFont:lbl_specialNotesValue.font];
            [lblSpecialInstruction setText:@"Special Instruction"];
           
             [lblSpecialInstructionValue setText:[dict_ChildData safeObjectForKey:kNote_aboutJob]];
            [lbl_specialNotesValue setNumberOfLines:10];
            [lbl_specialNotesValue sizeToFit];
            [lblSpecialInstructionValue setNumberOfLines:10];
            [lblSpecialInstructionValue sizeToFit];
            [view_specialNotes addSubview:lblSpecialInstruction];
            [view_specialNotes addSubview:lblSpecialInstructionValue];
            if ([[[dict_ChildData safeObjectForKey:kJobStatus] lowercaseString] isEqualToString:@"inactive"]) {
                scrollContentSize=440+(lblSpecialInstructionValue.frame.origin.y+lblSpecialInstructionValue.frame.size.height);
            }else{
                //640=y position of special notes view
                scrollContentSize=640+(lblSpecialInstructionValue.frame.origin.y+lblSpecialInstructionValue.frame.size.height);
            }
        }else{
            if ([[[dict_ChildData safeObjectForKey:kJobStatus] lowercaseString] isEqualToString:@"inactive"]) {
                scrollContentSize=440;
            }else{
                scrollContentSize=640;
            }
            
        }
    }else{
        [lbl_specialNotesValue setHidden:NO];
        [lbl_specialNotes setHidden:NO];
        //Special notes
        [lbl_specialNotesValue setBackgroundColor:[UIColor clearColor]];
       
        lbl_specialNotesValue.text=[dict_ChildData safeObjectForKey:kJobSpecialNeed];
        [lbl_specialNotesValue setVerticalAlignment:VerticalAlignmentTop];
        [lbl_specialNotesValue setNumberOfLines:10];
        [lbl_specialNotesValue sizeToFit];
        
        if (![[dict_ChildData safeObjectForKey:kNote_aboutJob] isEqualToString:@""]){
            
            UILabel *lblSpecialInstruction=[[UILabel alloc]initWithFrame:CGRectMake(lbl_specialNotes.frame.origin.x, lbl_specialNotesValue.frame.origin.y+lbl_specialNotesValue.frame.size.height,150,lbl_specialNotes.frame.size.height)];
            [lblSpecialInstruction setTextColor:lbl_specialNotes.textColor];
            VerticallyAlignedLabel *lblSpecialInstructionValue=[[VerticallyAlignedLabel alloc]initWithFrame:CGRectMake(lbl_specialNotesValue.frame.origin.x, lblSpecialInstruction.frame.origin.y+lblSpecialInstruction.frame.size.height, view_specialNotes.frame.size.width-10,60)];
            [lblSpecialInstructionValue setVerticalAlignment:VerticalAlignmentTop];
            [lblSpecialInstructionValue setBackgroundColor:[UIColor clearColor]];
            [lblSpecialInstructionValue setTextColor:lbl_specialNotesValue.textColor];
            [lblSpecialInstruction setFont:lbl_specialNotes.font];
            [lblSpecialInstructionValue setFont:lbl_specialNotesValue.font];
            [lblSpecialInstruction setText:@"Special Instruction"];
            [lblSpecialInstructionValue setText:[dict_ChildData safeObjectForKey:kNote_aboutJob]];
            [lblSpecialInstructionValue setNumberOfLines:10];
            [lblSpecialInstructionValue sizeToFit];
            [view_specialNotes addSubview:lblSpecialInstruction];
            [view_specialNotes addSubview:lblSpecialInstructionValue];
            if ([[[dict_ChildData safeObjectForKey:kJobStatus] lowercaseString] isEqualToString:@"inactive"]) {
                scrollContentSize=440+(lblSpecialInstructionValue.frame.origin.y+lblSpecialInstructionValue.frame.size.height);
            }else{
                //640=y position of special notes view
                scrollContentSize=640+(lblSpecialInstructionValue.frame.origin.y+lblSpecialInstructionValue.frame.size.height);
            }
            
            
        }
        
    }
    if ([[[dict_ChildData safeObjectForKey:@"is_expired"] lowercaseString] isEqualToString:@"yes"]) {
        [btn_cancelJob setTitle:@"Modify Job" forState:UIControlStateNormal];
        [btn_cancelJob setTag:100];
        [btn_cancelJob addTarget:self action:@selector(onClicked_modifyJob:) forControlEvents:UIControlEventTouchUpInside];
    }else{
        [btn_cancelJob setTitle:@"Cancel Job" forState:UIControlStateNormal];
        [btn_cancelJob setTag:101];
        [btn_cancelJob addTarget:self action:@selector(onClicked_cancelJobs:) forControlEvents:UIControlEventTouchUpInside];
    }
    
    [self performSelector:@selector(setScrollContentSize) withObject:nil afterDelay:0.03];
    
    
}
-(void)setScrollContentSize{
    DDLogInfo(@"scrl content size %f",scrollContentSize);
    self.backgroundScrollView.scrollEnabled=YES;
    [self.backgroundScrollView setContentSize:CGSizeMake(self.view.bounds.size.width,scrollContentSize)];
    [self.view layoutIfNeeded];
}
/*
#pragma mark - Navigation

// In a storyboard-based application, you will often want to do a little preparation before navigation
- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    // Get the new view controller using [segue destinationViewController].
    // Pass the selected object to the new view controller.
}
*/
#pragma mark - User define metohds
-(void)setTapGestureForPhone{
    UITapGestureRecognizer *tapForPhone1=[[UITapGestureRecognizer alloc]initWithTarget:self action:@selector(onTapPhoneNumber:)];
    [lbl_parentPhone1Value setTag:0];
    [lbl_parentPhone1Value addGestureRecognizer:tapForPhone1];
    [lbl_parentPhone1Value setUserInteractionEnabled:YES];
    
    
    UITapGestureRecognizer *tapForPhone2=[[UITapGestureRecognizer alloc]initWithTarget:self action:@selector(onTapPhoneNumber:)];
    [lbl_parentPhone2Value setTag:1];
    [lbl_parentPhone2Value addGestureRecognizer:tapForPhone2];
    [lbl_parentPhone2Value setUserInteractionEnabled:YES];
    
    
    UITapGestureRecognizer *tapForGuardianPhone1=[[UITapGestureRecognizer alloc]initWithTarget:self action:@selector(onTapPhoneNumber:)];
    [lbl_guardianPhone1Value setTag:2];
    [lbl_guardianPhone1Value addGestureRecognizer:tapForGuardianPhone1];
    [lbl_guardianPhone1Value setUserInteractionEnabled:YES];
    
    UITapGestureRecognizer *tapForGuardianPhone2=[[UITapGestureRecognizer alloc]initWithTarget:self action:@selector(onTapPhoneNumber:)];
    [lbl_guardianPhone2Value setTag:3];
    [lbl_guardianPhone2Value addGestureRecognizer:tapForGuardianPhone2];
    [lbl_guardianPhone2Value setUserInteractionEnabled:YES];
    
    UITapGestureRecognizer *tapForEmergencyContactPhone1=[[UITapGestureRecognizer alloc]initWithTarget:self action:@selector(onTapPhoneNumber:)];
    [lbl_emergencyContactPhone1Value setTag:4];
    [lbl_emergencyContactPhone1Value addGestureRecognizer:tapForEmergencyContactPhone1];
    [lbl_emergencyContactPhone1Value setUserInteractionEnabled:YES];
    
    UITapGestureRecognizer *tapForParentTap=[[UITapGestureRecognizer alloc]initWithTarget:self action:@selector(onTapParentEmailTap:)];
    [lbl_ParentEmailValue setTag:5];
    [lbl_ParentEmailValue addGestureRecognizer:tapForParentTap];
    [lbl_ParentEmailValue setUserInteractionEnabled:YES];
}
-(void)onTapPhoneNumber:(UITapGestureRecognizer*)gesture{
    DDLogInfo(@">>> %ld", (long)gesture.view.tag);
    NSInteger lblTag=gesture.view.tag;
    NSURL *phoneUrl=[NSURL URLWithString:@""];
    switch (lblTag) {
        case 0:
            phoneUrl = [NSURL URLWithString:[NSString stringWithFormat:@"telprompt:%@",[[dict_ChildData safeObjectForKey:kParentInfo] safeObjectForKey:kPhone]]];
            [self openPhoneUrl:phoneUrl];
            break;
        case 1:
            phoneUrl = [NSURL URLWithString:[NSString stringWithFormat:@"telprompt:%@",[[dict_ChildData safeObjectForKey:kParentInfo] safeObjectForKey:kLocal_Phone]]];
            [self openPhoneUrl:phoneUrl];
            break;
        case 2:
            phoneUrl = [NSURL URLWithString:[NSString stringWithFormat:@"telprompt:%@",[dict_ChildData safeObjectForKey:kGuardianPhone1]]];
            [self openPhoneUrl:phoneUrl];
            break;
        case 3:
            phoneUrl = [NSURL URLWithString:[NSString stringWithFormat:@"telprompt:%@",[dict_ChildData safeObjectForKey:kGuardianPhone2]]];
            [self openPhoneUrl:phoneUrl];
            break;
        case 4:
            phoneUrl = [NSURL URLWithString:[NSString stringWithFormat:@"telprompt:%@",[dict_ChildData safeObjectForKey:kEmergencyPhone]]];
            [self openPhoneUrl:phoneUrl];
            break;
        default:
            break;
    }
    
    
}
-(void)openPhoneUrl:(NSURL*)phoneUrl{
    DDLogInfo(@"Url %@",phoneUrl);
    if ([[UIApplication sharedApplication] canOpenURL:phoneUrl]) {
        [[UIApplication sharedApplication] openURL:phoneUrl];
    } else {
        UIAlertView * calert = [[UIAlertView alloc]initWithTitle:@"Alert" message:kCallFacilityNotAvailable delegate:nil cancelButtonTitle:@"ok" otherButtonTitles:nil, nil];
        [calert show];
    }
}
-(void)onTapParentEmailTap:(UIGestureRecognizer*)gesture
{
    DDLogInfo(@">>> %ld", (long)gesture.view.tag);
    if ([MFMailComposeViewController canSendMail]) {
        MFMailComposeViewController * emailController = [[MFMailComposeViewController alloc] init];
        [emailController.navigationBar setTintColor:[UIColor whiteColor]];
        emailController.mailComposeDelegate = self;
        [emailController setToRecipients:[NSArray arrayWithObjects:lbl_ParentEmailValue.text,nil]];
        [[emailController navigationBar]setTitleTextAttributes:[NSDictionary dictionaryWithObjectsAndKeys:[UIColor whiteColor], NSForegroundColorAttributeName, nil]];
        NSMutableString *emailBody = [[NSMutableString alloc] initWithString:@"<html><body>"];
        //Add some text to it however you want
        [emailBody appendString:@"<p></p>"];
        [emailBody appendString:@"<br>"];
        
        [emailBody appendString:@"</body></html>"];
        [emailBody appendString:@"<br>"];
        [emailController setMessageBody:emailBody isHTML:YES];
        
        [self presentViewController:emailController animated:YES completion:^{
            
        }];
    }
    else {
        UIAlertView * alertView = [[UIAlertView alloc] initWithTitle:@"Warning" message:kMustMailAccount delegate:nil cancelButtonTitle:NSLocalizedString(@"OK", @"OK") otherButtonTitles:nil];
        [alertView show];
        
    }
    
}
- (IBAction)onClicked_modifyJob:(id)sender {
    UIButton *btn=(UIButton*)sender;
     ModifyJobViewController *viewModifyJob=[[ModifyJobViewController alloc]initWithNibName:@"ModifyJobViewController" bundle:nil];
    if (btn.tag==100) {
        viewModifyJob.indexPath=indexPath;
        viewModifyJob.isExpired=@"Yes";
        viewModifyJob.dict_jobData=dict_ChildData;
    }else{
        viewModifyJob.indexPath=indexPath;
        viewModifyJob.dict_jobData=dict_ChildData;
        viewModifyJob.isExpired=@"No";
    }
    
    self.navigationItem.title=@"";
     [self.navigationController pushViewController:viewModifyJob animated:YES];
}
- (IBAction)onClicked_cancelJobs:(id)sender {
    
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:kConfirmationMsgForCancelJob delegate:self cancelButtonTitle:@"Yes" otherButtonTitles:@"No", nil];
    [alert setTag:100];
    [alert show];
  
}
- (IBAction)onClickNext:(id)sender {
    if (pageNo < totalCount) {
        btn_next.hidden = false;
        btn_previous.hidden = true;
        pageNo++;
        [scroll_View setContentOffset:CGPointMake(scroll_View.frame.size.width*pageNo, scroll_View.contentOffset.y) animated:YES];
        if (pageNo == totalCount) {
            btn_next.hidden = false;
        }
    }
    else
        btn_next.hidden = false;
}

- (IBAction)onClickPrevious:(id)sender {
    if (pageNo > 0) {
        btn_previous.hidden = false;
        btn_next.hidden = false;
        
        pageNo--;
        [scroll_View setContentOffset:CGPointMake(scroll_View.frame.size.width*pageNo, scroll_View.contentOffset.y) animated:YES];
        if (pageNo == 0) {
            btn_previous.hidden = true;
        }
        
    }
    else
        btn_previous.hidden = true;
    
}
- (void)scrollViewDidScroll:(UIScrollView *)scrollView {
    if (((int)scrollView.contentOffset.x % (int)scrollView.frame.size.width) == 0) {
        currentPage = scrollView.contentOffset.x /scrollView.frame.size.width;
        pageNo = currentPage;
        if (pageNo == 0) {
            btn_previous.hidden = true;
            btn_next.hidden = false;
        }
        else
            btn_previous.hidden = false;
        if (pageNo==totalCount) {
            btn_next.hidden = true;
            btn_previous.hidden = false;
        }
        else
            btn_next.hidden = false;
        if (arrChildData.count == 1) {
            btn_next.hidden = true;
            btn_previous.hidden = true;
        }
    }
    
}
#pragma mark - MFMailComposeViewControllerDelegate

- (void)mailComposeController:(MFMailComposeViewController *)controller didFinishWithResult:(MFMailComposeResult)result error:(NSError *)error {
    NSInteger results = result;
    switch(results){
        case MFMailComposeResultSent:{
            [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kEmailSendSuccessFully];
            break;
        }
        case MFMailComposeResultCancelled:{
            [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kEmailSendingCancelled];
            break;
        }
        case MFMailComposeResultFailed:{
            [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kEmailSendingFailed];
            break;
        }
    }
    [controller dismissViewControllerAnimated:YES completion:^{
        
    }];
}
-(void)getJobDetail:(int)jobID{
    SMCoreNetworkManager *networkManager;
    NSString *string_Url=[NSString stringWithFormat:@"%@",kJobDetail_API];
    networkManager= [[SMCoreNetworkManager alloc] initWithBaseURLString:string_Url];
    networkManager.delegate = self;
    NSMutableDictionary *dict_JobRequest=[[NSMutableDictionary alloc] init];
    [dict_JobRequest setSafeObject:self.sitterInfo.sitterId forKey:kUserId];
    [dict_JobRequest setSafeObject:self.sitterInfo.str_TokenData forKey:kToken];
    [dict_JobRequest setSafeObject:[NSNumber numberWithInt:self.jobId] forKey:kJobId];
    [dict_JobRequest setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [networkManager getJobDetail:dict_JobRequest forRequestNumber:2];
}
#pragma mark - AlertView delegate
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    if (alertView.tag==100 && buttonIndex==0) {
        [self startNetworkActivity:NO];
        SMCoreNetworkManager *networkManager;
        NSString *string_Url=[NSString stringWithFormat:@"%@",kCancelJob_API];
        networkManager= [[SMCoreNetworkManager alloc] initWithBaseURLString:string_Url];
        networkManager.delegate = self;
        NSMutableDictionary *dict_JobRequest=[[NSMutableDictionary alloc] init];
        [dict_JobRequest setSafeObject:self.sitterInfo.sitterId forKey:kUserId];
        [dict_JobRequest setSafeObject:self.sitterInfo.str_TokenData forKey:kToken];
        [dict_JobRequest setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
        [dict_JobRequest setSafeObject:[dict_ChildData safeObjectForKey:kJobId] forKey:kJobId];
        [networkManager cancelJob:dict_JobRequest forRequestNumber:1];
        
    }else if (alertView.tag==101 && buttonIndex==0){
        [self.navigationController popToRootViewControllerAnimated:YES];
    }
    [self appDidLogout:alertView clickedButtonAtIndex:buttonIndex];
}
#pragma mark - SMCoreNetworkManagerDelegate
-(void)requestDidSucceedWithResponseObject:(id)responseObject
withTask:(NSURLSessionDataTask *)task
withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    switch (requestId) {
        case 1:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:[dict_responseObj valueForKey:kMessage] delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
                [alert setTag:101];
                [alert show];
             
                    }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            break;
        case 2://Get job detail
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                DDLogInfo(@"job detail %@",dict_responseObj);
                dict_ChildData =[[dict_responseObj safeObjectForKey:kData]safeObjectForKey:@"jobDetails"];
                [self showChildDetail];
                [self.view setNeedsDisplay];
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
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
@end


