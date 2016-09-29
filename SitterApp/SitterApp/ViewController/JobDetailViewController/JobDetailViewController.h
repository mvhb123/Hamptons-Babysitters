//
//  JobDetailViewController.h
//  SitterApp
//
//  Created by Shilpa Gade on 09/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>
@interface JobDetailViewController : AppBaseViewController
{
    __weak IBOutlet UILabel *lbl_JobNumber;
    __weak IBOutlet UILabel *lbl_StartDate;
    __weak IBOutlet UILabel *lbl_EndDate;
    __weak IBOutlet UILabel *lbl_ChildrenCount;
    __weak IBOutlet UILabel *lbl_Address;
    __weak IBOutlet UILabel *lbl_Area;
    __weak IBOutlet UILabel *lbl_JobStatus;
    __weak IBOutlet UILabel *lbl_Rate;
    __weak IBOutlet UILabel *lbl_Total;
    __weak IBOutlet UILabel *lbl_SpecialNeeds;
    __weak IBOutlet UILabel *lbl_ChildDetail;
    __weak IBOutlet UILabel *lbl_ShowJobNumber;
    __weak IBOutlet UILabel *lbl_ShowStartDate;
    __weak IBOutlet UILabel *lbl_ShowEndDate;
    __weak IBOutlet UILabel *lbl_ShowRate;
    __weak IBOutlet UILabel *lbl_ShowTotal;
    __weak IBOutlet UILabel *lbl_ShowSpecialNeeds;
    __weak IBOutlet UILabel *lbl_ShowChildCount;
    __weak IBOutlet VerticallyAlignedLabel *lbl_ShowAddress;
    __weak IBOutlet UILabel *lbl_ShowJobStatus;
    __weak IBOutlet UILabel *lbl_ShowArea;
    __weak IBOutlet UILabel *lbl_City;
    __weak IBOutlet UILabel *lbl_ShowCity;
    __weak IBOutlet UILabel *lbl_State;
    __weak IBOutlet UILabel *lbl_ShowState;
    __weak IBOutlet UILabel *lbl_ZipCode;
    __weak IBOutlet UILabel *lbl_ShowZipCode;
    __weak IBOutlet UITableView *tbl_ChildDetail;
    __weak IBOutlet UITextView *txt_SpecialNeeds;
    __weak IBOutlet UIButton *btn_bookJob;
    __weak IBOutlet UIScrollView *scroll_View;
    NSDictionary *dict_jobDetail;
    
    //For next Previous
    int pageNo;
    int totalCount;
    int currentPage;
    __weak IBOutlet UIButton *btn_previous;
    __weak IBOutlet UIButton *btn_next;
    NSMutableArray *arrChildData;

    //Constraint
    __weak IBOutlet NSLayoutConstraint *conForbtnYpos;
    __weak IBOutlet UIView *view_bottom;
    __weak IBOutlet UILabel *lbl_specialInstruction;
    __weak IBOutlet UITextView *txt_specialInstruction;
    __weak IBOutlet NSLayoutConstraint *consForSpecialistruction;
    float scrollContentSize;

    
}

@property(assign)int indexPath,flag;
@property(nonatomic,strong)NSMutableArray *array_Jobs;
@property(nonatomic,weak)JobList *jobList;
@property(nonatomic,weak)Sitter *sitterInfo;
@property(nonatomic,assign)int jobId;
-(void)getJobDetail:(int)jobID;
- (IBAction)onClick_BookJob:(id)sender;
- (IBAction)onClickNext:(id)sender;
- (IBAction)onClickPrevious:(id)sender;
@end
